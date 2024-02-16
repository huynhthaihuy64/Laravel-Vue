<?php

namespace App\Http\Services;

use App\Exceptions\AppValidationException;
use App\Exceptions\NotFoundException;
use App\Mail\ThankForBuy;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Paypal;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Str;

class CartService
{
    protected $user;
    protected $cart;
    protected $product;
    protected $customer;
    protected $userProduct;
    protected $paypal;

    public function __construct(User $user, Product $product, Cart $cart, Customer $customer, UserProduct $userProduct, Paypal $paypal)
    {
        $this->user = $user;
        $this->product = $product;
        $this->cart = $cart;
        $this->customer = $customer;
        $this->userProduct = $userProduct;
        $this->paypal = $paypal;
    }
    public function index($params)
    {
        $carts = $this->cart->with('product', 'user')->paginate($params['limit'] ?? 10);
        $totalAll = 0;
        foreach ($carts as $cart) {
            $totalAll += $cart->total;
        }
        $data = [
            'carts' => $carts,
            'total_all' => $totalAll
        ];
        return $data;
    }

    public function store($params)
    {
        $user = auth()->user();
        $product = $this->product->find($params['product_id']);
        if (!$product) {
            throw new NotFoundException(Product::class);
        }
        try {
            DB::beginTransaction();
            $checkCart = $this->cart->where('user_id', $user->id)->where('product_id', $product->id)->first();
            if ($checkCart) {
                throw new AppValidationException(__('messages.validation.cart.exists'));
            }
            $cart = $this->cart->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'qty' => $params['qty'],
                'price' => $product->price,
                'total' => $params['qty'] * $product->price
            ]);
            DB::commit();
            return $cart;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show($id)
    {
        $cart = $this->cart->find($id);
        if (!$cart) {
            throw new NotFoundException(Cart::class);
        }
        return $cart;
    }

    public function update($params)
    {
        try {
            DB::beginTransaction();
            if (is_array($params['carts'])) {
                foreach ($params['carts'] as $val) {
                    $cart = $this->cart->find($val['id']);
                    if (!$cart) {
                        throw new NotFoundException(Cart::class);
                    }
                    $cart->update([
                        'qty' => $val['qty'],
                        'total' => $cart->price * $val['qty']
                    ]);
                }
            } else {
                $cart = $this->cart->find($params['carts']['id']);
                if (!$cart) {
                    throw new NotFoundException(Cart::class);
                }
                $cart->update([
                    'qty' => $params['carts']['qty'],
                    'total' => $cart->price * $params['carts']['qty']
                ]);
            }

            DB::commit();
            return $cart;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function destroy($ids)
    {
        try {
            DB::beginTransaction();
            if (is_array($ids)) {
                $this->cart->whereIn('id', $ids)->delete();
            } else {
                $this->cart->where('id', $ids)->delete();
            }
            DB::commit();
            return ['message' => __('messages.cart.delete.success')];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function payment($data)
    {
        $amount = $this->cart->whereIn('id', array_column($data['carts'], 'id'))->select(DB::raw("CAST(SUM(total) AS UNSIGNED) as amount"))->value('amount');
        switch ($data['method']) {
            case "Paypal":
                $paypal = $this->paypal($amount);
                $this->addPayment($data, $paypal);
                return response()->json(['link' => $paypal['link']]);
            case "VNPay":
                $vnPay = $this->vnPay($data);
                $vnPay['token'] = (string)Str::uuid();
                $this->addPayment($data, $vnPay);
                return response()->json(['link' => $vnPay['link']]);
            case "OnePay":
                break;
            case "Momo":
                $this->paymentMomo($data);
                break;
        }
    }

    public function paypal($amount)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('cart.success', ['method' => 'Paypal']),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => __('messages.cart.payment.currency_code'),
                        "value" => $amount
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return ['link' => $link['href'], 'token' => $response['id']];
                }
            }
        } else {
            return response()->json(['message' => __('messages.cart.payment.failed')]);
        }
    }

    public function success($param)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($param->token);
        $data = $param->toArray();
        $paypal = $this->paypal->where('token', $data['token'])->get()->toArray();
        if (isset($response['status']) && $response['status'] === __('messages.cart.payment.complete')) {
            DB::beginTransaction();
            return $this->handlePayment($paypal, $param);
        } else {
            return response()->json(['message' => __('messages.cart.payment.failed')]);
        }
    }

    private function vnPay($param)
    {
        $vnpUrl = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $codeCart = rand(00, 9999);
        $vnpReturnUrl = route('cart.success');
        $vnpTmnCode = "J84XCQOL"; //Mã website tại VNPAY 
        $vnpHashSecret = "VTBZDFMGOGMTAZIPGFLEWAYAENKVNZQQ"; //Chuỗi bí mật

        $vnpTxnRef = $codeCart; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnpOrderInfo = 'Test Đơn Hàng';
        $vnpOrderType = 'billpayment';
        $vnpAmount = 10000 * 100;
        $vnpLocale = 'vn';
        $vnpBankCode = 'NCB';
        $vnpIpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnpTmnCode,
            "vnp_Amount" => $vnpAmount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnpIpAddr,
            "vnp_Locale" => $vnpLocale,
            "vnp_OrderInfo" => $vnpOrderInfo,
            "vnp_OrderType" => $vnpOrderType,
            "vnp_ReturnUrl" => $vnpReturnUrl,
            "vnp_TxnRef" => $vnpTxnRef,
        );

        if (isset($vnpBankCode) && $vnpBankCode != "") {
            $inputData['vnp_BankCode'] = $vnpBankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnpUrl . "?" . $query;
        if (isset($vnpHashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnpHashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00', 'message' => 'success', 'link' => $vnp_Url
        );
        if (isset($_POST['redirect'])) {
            header('Location: ' . $vnp_Url);
            die();
        } else {
            return $returnData;
        }
    }

    private function addPayment(array $data, array $payment)
    {
        if (isset($payment['link'])) {
            foreach ($data['carts'] as $cart) {
                $this->paypal->create([
                    'token' => $payment['token'],
                    'cart_id' => $cart['id'],
                    'user_id' => auth()->user()->id,
                    'method' => $data['method'],
                ]);
            }
        }
    }

    private function handlePayment($data, $param)
    {
        try {
            foreach ($data as $val) {
                $cart = $this->cart->where('id', $val['cart_id'])->first();
                if (!$cart) {
                    throw new NotFoundException(Cart::class);
                }
                $product = $this->product->find($cart->product_id);
                if (!$product) {
                    throw new NotFoundException(Product::class);
                }
                $this->userProduct->create([
                    'user_id' => auth()->user()->id,
                    'product_id' => $product->id,
                    'qty' => $cart->qty,
                    'price' => $cart->price,
                    'total' => $cart->total,
                ]);
            }
            $dataBuy = $this->cart->whereIn('id', array_column($data, 'cart_id'))->with('product')->get()->toArray();
            $this->cart->whereIn('id', array_column($data, 'cart_id'))->delete();
            $user = $this->user->where('id', $data[0]['user_id'])->first()?->toArray();
            Mail::to($user['email'])->send(new ThankForBuy($param, $dataBuy));
            DB::commit();
            return response()->json(['message' => __('messages.cart.payment.success')]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['message' => __('messages.cart.payment.failed')]);
        }
    }
}
