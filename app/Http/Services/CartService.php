<?php

namespace App\Http\Services;

use App\Exceptions\NotFoundException;
use App\Mail\ThankForBuy;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Paypal;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProduct;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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
        $jsonFilePath = storage_path('setting.json');
        $jsonData = file_get_contents($jsonFilePath);
        $data = json_decode($jsonData, true);
        foreach ($carts as $cart) {
            $totalAll += $cart->total;
        }
        $data = [
            'carts' => $carts,
            'total_all' => round($data[auth()->user()->currency]['value'] * $totalAll,2)
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
                throw new Exception(__('messages.validation.cart.exists'));
            }
            $price = $product->price_sale < $product->price ? $product->price_sale : $product->price;
            $cart = $this->cart->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'qty' => $params['qty'],
                'price' => $price,
                'total' => $params['qty'] * $price
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
            case env('PAYPAL_NAME'):
                $paypal = $this->paypal($amount);
                $this->addPayment($data, $paypal);
                return response()->json(['link' => $paypal['link']]);
            case "VNPay":
                $vnPay = $this->vnPay($data);
                $this->addPayment($data, $vnPay);
                return response()->json(['link' => $vnPay['link']]);
            case "OnePay":
                $onePay = $this->onePay($data);
                $this->addPayment($data, $onePay);
                return response()->json(['link' => $onePay['link']]);
            case "Momo":
                $momo = $this->paymentMomo($data);
                $this->addPayment($data, $momo);
                return response()->json(['link' => $momo['link']]);
        }
    }

    public function paypal($amount)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => env('PAYPAL_INTENT'),
            "application_context" => [
                "return_url" => route('cart.success', ['method' => env('PAYPAL_NAME')]),
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
                if ($link['rel'] === env('PAYPAL_RESPONSE')) {
                    return ['link' => $link['href'], 'token' => $response['id']];
                }
            }
        } else {
            return response()->json(['message' => __('messages.cart.payment.failed')]);
        }
    }

    public function success($param)
    {
        $data = $param->toArray();
        $code = '';
        if (isset($param['vnp_TxnRef'])) {
            $token = $data['vnp_TxnRef'];
            $code = $data['vnp_TransactionStatus'];
        } elseif(isset($data['orderId'])) {
            $token = $data['orderId'];
            $code = $data['errorCode'];
        } else {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($param->token);
            $token = $data['token'];
            $code = $response['status'];
        }
        $paypal = $this->paypal->where('token', $token)->get()->toArray();
        if ($paypal && in_array($code,['0','00', 'COMPLETED'])) {
            DB::beginTransaction();
            if (isset($data['token']) && isset($response['status']) && $response['status'] === __('messages.cart.payment.complete')) {
                return $this->handlePayment($paypal, $param);
            } else {
                return $this->handlePayment($paypal, $param);
            }
        } else {
            return Redirect::to(env('APP_URL') . '/cart?status=fail');
        }
    }

    private function vnPay($param)
    {
        $vnpUrl = env('VNPAY_URL');
        $codeCart = rand(00, 9999);
        $vnpReturnUrl = route('cart.success');
        $vnpTmnCode = env('VNPAY_TMNCODE'); //Mã website tại VNPAY 
        $vnpHashSecret = env('VNPAY_HASHSECRET'); //Chuỗi bí mật

        $vnpTxnRef = strval($codeCart); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnpOrderInfo = 'Test Đơn Hàng';
        $vnpOrderType = env('VNPAY_ORDERTYPE');
        $vnpAmount = 0;
        foreach($param['carts'] as $val) {
            $cart = $this->cart->where('id', $val['id'])->first()?->toArray();
            $vnpAmount += $cart['total'];
        }
        $vnpLocale = env('VNPAY_LOCALE');
        $vnpBankCode = 'NCB';
        $vnpIpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnpTmnCode,
            "vnp_Amount" => $vnpAmount * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => env('VNPAY_CURRENCY'),
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

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnpUrl . "?" . $query;
        if (isset($vnpHashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashData, $vnpHashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00', 'message' => 'success', 'link' => $vnp_Url, 'token' => $vnpTxnRef, 'bank' => $vnpBankCode
        );
        if (isset($_POST['redirect'])) {
            return $vnp_Url;
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
                    'bank_name' => $payment['bank'] ?? ''
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
                $inventory = $product->inventory_number - $cart->qty;
                if ($inventory < 0) {
                    throw new BadRequestException(__('Insufficient inventory'));
                }
                $product->update([
                    'inventory_number' => $inventory,
                ]);
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
            // return response()->json(['message' => __('messages.cart.payment.success')]);
            return redirect()->to(env('APP_URL'). '/cart?status=success');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return
            redirect()->to(env('APP_URL') . '/cart?status=fail');
        }
    }

    private function paymentMomo($data)
    {
        $endpoint = env('MOMO_URL');

        $partnerCode = env('MOMO_PARTNERCODE');
        $accessKey = env('MOMO_ACCESSKEY');
        $secretKey = env('MOMO_SECRETKEY');

        $orderInfo = "Thanh toán qua MoMo";
        $amount = 0;
        foreach ($data['carts'] as $val) {
            $cart = $this->cart->where('id', $val['id'])->first()?->toArray();
            $amount += $cart['total'];
        }
        $orderId = time() . "";
        $returnUrl = route('cart.success');
        $notifyurl = route('cart.success');
        // Lưu ý: link notifyUrl không phải là dạng localhost
        $bankCode = "VCB";
        $requestId = time() . "";
        $requestType = env('MOMO_REQUEST_TYPE');
        $extraData = "";
        $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&bankCode=" . $bankCode . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&returnUrl=" . $returnUrl . "&notifyUrl=" . $notifyurl . "&extraData=" . $extraData . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data =  array(
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => strval($amount),
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'returnUrl' => $returnUrl,
            'bankCode' => $bankCode,
            'notifyUrl' => $notifyurl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json
        error_log(print_r($jsonResult, true));
        $info = [
            'link' => $jsonResult['payUrl'],
            'bank' => $bankCode,
            'token' => $orderId
        ];
        return $info;
    }

    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    private function onePay($data) {
        $SECURE_SECRET = env('ONEPAY_SECURE_SECRET');

        $vpcURL = env('ONEPAY_URL') . "?";
        $amount = 0;
        foreach ($data['carts'] as $val) {
            $cart = $this->cart->where('id', $val['id'])->first()?->toArray();
            $amount += $cart['total'];
        }
        $params = [
            'vpc_Merchant' => env('ONEPAY_MERCHANT'),
            'vpc_AccessCode' => env('ONEPAY_ACCESSCODE'),
            'vpc_MerchTxnRef' => time() . "",
            'vpc_OrderInfo' => env('ONEPAY_ORDER_INFO'),
            'vpc_Amount' => $amount,
            'vpc_ReturnURL' => route('cart.success'),
            // 'vpc_Version' => '2',
            'vpc_Command' => env('ONEPAY_COMMAND'),
            'vpc_Locale' => env('ONEPAY_LOCALE'),
            'vpc_Currency' => env('ONEPAY_CURRENCY')
        ];
        $stringHashData = "";
        ksort($params);

        $appendAmp = 0;

        foreach ($params as $key => $value) {

            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
            
                if ((strlen($value) > 0) && ((substr($key, 0, 4) == "vpc_") || (substr($key, 0, 5) == "user_"))) {
                    $stringHashData .= $key . "=" . $value . "&";
                }
            }
        }
        
        $stringHashData = rtrim($stringHashData, "&");
        if (strlen($SECURE_SECRET) > 0) {
            $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', $SECURE_SECRET)));
        }

        $result = [
            'token' => $params['vpc_MerchTxnRef'],
            'link' => $vpcURL
        ];
        return $result;
    }
}
