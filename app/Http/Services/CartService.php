<?php

namespace App\Http\Services;

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
            throw new \Exception('Product not found');
        }
        try {
            DB::beginTransaction();
            $checkCart = $this->cart->where('user_id', $user->id)->where('product_id', $product->id)->first();
            if ($checkCart) {
                throw new \Exception('You already have it in your cart');
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
            throw new \Exception("Cart Not Found");
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
                        throw new \Exception("Cart Not Found");
                    }
                    $cart->update([
                        'qty' => $val['qty'],
                        'total' => $cart->price * $val['qty']
                    ]);
                }
            } else {
                $cart = $this->cart->find($params['carts']['id']);
                if (!$cart) {
                    throw new \Exception("Cart Not Found");
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
            return ['message' => "Delete Success"];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function payment($data)
    {
        $amount = $this->cart->whereIn('id', array_column($data['carts'], 'id'))->select(DB::raw("CAST(SUM(total) AS UNSIGNED) as amount"))->value('amount');
        $paypal = $this->paypal($amount);
        if (isset($paypal['link'])) {
            foreach ($data['carts'] as $cart) {
                $this->paypal->create([
                    'token' => $paypal['token'],
                    'cart_id' => $cart['id'],
                    'user_id' => auth()->user()->id
                ]);
            }
        }
        return response()->json(['link' => $paypal['link']]);
    }

    public function paypal($amount)
    {
        $provider = new PayPalClient;

        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('cart.success'),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
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
            return response()->json(['message' => "Can't Payment"]);
        }
    }

    public function success($param)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($param->token);
        $data = $param->toArray();
        $paypal = $this->paypal->where('token',$data['token'])->get()->toArray();
        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            DB::beginTransaction();
            try {
                foreach ($paypal as $val) {
                    $cart = $this->cart->where('id', $val['cart_id'])->first();
                    if (!$cart) {
                        throw new \Exception('Cart Not Found');
                    }
                    $product = $this->product->find($cart->product_id);
                    if (!$product) {
                        throw new \Exception('Product Not Found');
                    }
                    $userProduct = $this->userProduct->create([
                        'user_id' => auth()->user()->id,
                        'product_id' => $product->id,
                        'qty' => $cart->qty,
                        'price' => $cart->price,
                        'total' => $cart->total,
                    ]);
                }
                $dataBuy = $this->cart->whereIn('id', array_column($paypal, 'cart_id'))->with('product')->get()->toArray();
                $this->cart->whereIn('id', array_column($paypal, 'cart_id'))->delete();
                $user = $this->user->where('id', $paypal[0]['user_id'])->first()?->toArray();
                $this->paypal->where('token', $data['token'])->delete();
                Mail::to($user['email'])->send(new ThankForBuy($param, $dataBuy));
                DB::commit();
                return response()->json(['message' => "Payment Success"]);
            } catch (\Exception $e) {
                DB::rollback();
                Log::info($e->getMessage());
                return response()->json(['message' => "Can't Payment"]);
            }
        } else {
            return response()->json(['message' => "Can't Payment"]);
        }
    }
}
