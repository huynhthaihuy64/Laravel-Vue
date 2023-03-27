<?php

namespace App\Http\Services;

use App\Mail\OrderShipped;
use App\Models\Cart as ModelsCart;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CartService
{
    public function payment($data) {
        try {
            DB::beginTransaction();
            $str = str_replace(',', '', Cart::total());
            $str = explode('.',$str);
            $total_tax = $str[0];
            $customer = Customer::create([
                'user_id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'content' => $data['content'] ?? null,
                'total_tax' => intval($total_tax),
            ]);
            foreach($data['carts'] as $val) {
                $cart = Cart::get($val['rowId'])->toArray();
                ModelsCart::create([
                    'customer_id' => $customer->id,
                    'product_id' => $cart['id'],
                    'qty' => $cart['qty'],
                    'price' => $cart['price'],
                    'total' => $cart['subtotal'],
                ]);
            }
            DB::commit();
            Cart::destroy();
            return response()->json(['message' => "Payment Success"]);
        } catch(\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['message' => "Can't Payment"]);
        }
    }
}
