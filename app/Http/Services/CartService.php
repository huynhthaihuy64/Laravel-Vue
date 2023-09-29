<?php

namespace App\Http\Services;

use App\Mail\ThankForBuy;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CartService
{
    protected $user;
    protected $cart;
    protected $product;
    protected $customer;
    protected $userProduct;

    public function __construct(User $user, Product $product , Cart $cart, Customer $customer, UserProduct $userProduct)
    {
        $this->user = $user;
        $this->product = $product;
        $this->cart = $cart;
        $this->customer = $customer;
        $this->userProduct = $userProduct;
    }
    public function index ($params) {
        $carts = $this->cart->with('product','user')->paginate($params['limit'] ?? 10);
        $totalAll = 0;
        foreach($carts as $cart) {
            $totalAll += $cart->total;
        }
        $data = [
            'carts' => $carts,
            'total_all' => $totalAll
        ];
        return $data;
    }

    public function store($params) {
        $user = auth()->user();
        $product = $this->product->find($params['product_id']);
        if(!$product) {
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
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show($id) {
        $cart = $this->cart->find($id);
        if(!$cart) {
            throw new \Exception("Cart Not Found");
        }
        return $cart;
    }

    public function update($params) {
        try {
            DB::beginTransaction();
            if (is_array($params['carts'])) {
                foreach($params['carts'] as $val){
                    $cart = $this->cart->find($val['id']);
                    if(!$cart) {
                        throw new \Exception("Cart Not Found");
                    }
                    $cart->update([
                        'qty' => $val['qty'],
                        'total' => $cart->price * $val['qty']
                    ]);
                }
            } else {
                $cart = $this->cart->find($params['carts']['id']);
                if(!$cart) {
                    throw new \Exception("Cart Not Found");
                }
                $cart->update([
                    'qty' => $params['carts']['qty'],
                    'total' => $cart->price * $params['carts']['qty']
                ]);
            }
            
            DB::commit();
            return $cart;
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function destroy($ids) {
        try {
            DB::beginTransaction();
            if(is_array($ids)) {
                $this->cart->whereIn('id',$ids)->delete();
            } else {
                $this->cart->where('id',$ids)->delete();
            }
            DB::commit();
            return ['message' => "Delete Success"];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function payment($data) {
        try {
            DB::beginTransaction();
            foreach($data['carts'] as $val) {
                $cart = $this->cart->where('id',$val['id'])->where('user_id',auth()->user()->id)->first();
                if(!$cart) {
                    throw new \Exception('Cart Not Found');
                }
                $product = $this->product->find($cart->product_id);
                if(!$product) {
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
            $dataBuy = $this->cart->whereIn('id',array_column($data['carts'], 'id'))->with('product')->get()->toArray();
            $this->cart->whereIn('id',array_column($data['carts'], 'id'))->delete();
            Mail::to($data['email'])->send(new ThankForBuy($data,$dataBuy));
            DB::commit();
            return response()->json(['message' => "Payment Success"]);
        } catch(\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['message' => "Can't Payment"]);
        }
    }
}
