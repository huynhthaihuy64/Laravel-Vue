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
    // public function create($request)
    // {
    //     $qty = (int)$request->input('num_product');
    //     $product_id = (int)$request->input('product_id');

    //     if ($qty <= 0 || $product_id <= 0) {
    //         session()->flash('error', 'Incorrect Quantity or Product');
    //         return false;
    //     }

    //     $carts = session()->get('carts');

    //     if (is_null($carts)) {
    //         session()->put('carts', [
    //             $product_id => $qty
    //         ]);
    //         return true;
    //     }

    //     $exists = Arr::exists($carts, $product_id);
    //     if ($exists) {
    //         $carts[$product_id] = $carts[$product_id] + $qty;
    //         session()->put('carts', $carts);
    //         return true;
    //     }

    //     $carts[$product_id] = $qty;
    //     session()->put('carts', $carts);

    //     return true;
    // }

    // public function getProduct()
    // {
    //     $carts = session()->get('carts');
    //     if (is_null($carts)) return [];

    //     $productId = array_keys($carts);
    //     return Product::select('id', 'name', 'price', 'price_sale', 'file')
    //         ->where('active', 1)
    //         ->whereIn('id', $productId)
    //         ->get();
    // }

    // public function update($request)
    // {
    //     session()->put('carts', $request->input('num_product'));
    //     return true;
    // }

    // public function remove($id)
    // {
    //     $carts = session()->get('carts');
    //     unset($carts[$id]);

    //     session()->put('carts', $carts);
    //     return true;
    // }

    // public function addCart($request)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $data = $request->toArray();

    //         $customer = Customer::create([
    //             'name' => $data['name'],
    //             'phone' => $data['phone'],
    //             'address' => $data['address'],
    //             'email' => $data['email'],
    //             'content' => $data['content'],
    //         ]);

    //         $this->infoProductCart($data['product_ids'], $customer->id);
    //         // $mail = $customer->email;
    //         // $details = [];
    //         // Mail::to($mail)->send(new OrderShipped($details));

    //         DB::commit();
    //         session()->flash('success', 'Order Success');

    //         session()->forget('carts');
    //         return true;
    //     } catch (\Exception $err) {
    //         DB::rollback();
    //         session()->flash('error', 'Order Failed');
    //         return false;
    //     }
    // }

    // public function infoProductCart($carts, $customer_id)
    // {
    //     $products = Product::select('id', 'name', 'price', 'price_sale', 'file')
    //         ->where('active', 1)
    //         ->whereIn('id', $carts)
    //         ->get();
    //     $data = [];

    //     foreach ($products as  $product) {
    //         $data[] = [
    //             'customer_id' => $customer_id,
    //             'product_id' => $product->id,
    //             'pty' => count($products),
    //             'price' => $product->price_sale != 0 ? $product->price_sale : $product->price,
    //         ];
    //     }
    //     dd(Cart::add('293ad', 'Product 1', 1, 9.99, 550, ['size' => 'large']));
    //     return Cart::add($data);
    // }

    // public function getCustomer()
    // {
    //     return Customer::orderBy('id', 'asc')->paginate(15);
    // }

    // public function getProductForCart($customer)
    // {
    //     return  $customer->carts()->with(['product' => function ($query) {
    //         $query->select('id', 'name', 'file');
    //     }])->get();
    // }

    // public function setCartValues() {
    //     $cartItems = Cart::instance('default')->content();
    //     $cartTaxRate = config('cart.tax');
    //     $tax = config('cart.tax') /100;
    //     $cartSubtotal = Cart::instance('default')->subtotal();
    //     $code =  session()->get('coupon')['name']??null;
    //     $discount =  session()->get('coupon')['discount']??0;
    //     $newSubtotal = ($cartSubtotal - $discount);
    //     if ($newSubtotal < 0) {
    //         $newSubtotal = 0;
    //     }
    //     $newTax = $newSubtotal * $tax;
    //     $newTotal = $newSubtotal * (1+$tax);
    //     $laterItems = Cart::instance('laterCart')->content();
    //     $laterCount = Cart::instance('laterCart')->count();

    //     return collect([
    //         'cartItems' => $cartItems,
    //         'cartTaxRate' => $cartTaxRate,
    //         'cartSubtotal' => $cartSubtotal,
    //         'code' => $code,
    //         'discount' => $discount,
    //         'newSubtotal' => $newSubtotal,
    //         'newTax' => $newTax,
    //         'newTotal' => $newTotal,
    //         'laterItems' => $laterItems,
    //         'laterCount' => $laterCount,
    //     ]);
    // }
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
                'phone' => $data['phone'],
                'address' => $data['address'],
                'content' => $data['content'],
                'total_tax' => intval($total_tax),
            ]);
            foreach($data['formData'] as $val) {
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
            return response()->json(['message' => "Payment Success"]);
        } catch(\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['message' => "Can't Payment"]);
        }
    }
}
