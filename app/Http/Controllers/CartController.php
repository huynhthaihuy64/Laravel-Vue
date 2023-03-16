<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\CartService;
use App\Http\Services\Product\ProductService;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    //
    protected $cartService;

    protected $product;

    public function __construct(CartService $cartService, ProductService $product)
    {
        $this->cartService = $cartService;
        $this->product = $product;
    }

    public function index()
    {
        $carts = Cart::content();
        $result = [
            'data' => $carts,
            'total' => Cart::priceTotal(),
            'tax' => Cart::tax(),
            'total_tax' => Cart::total(),
        ];
        return $result;
    }

    public function show(string $rowId) {
        $cart = Cart::get($rowId);
        return $cart;
    }

    public function update(Request $request) {
        $data = $request->toArray();
        foreach($data['formData'] as $value) {
            Cart::update($value['rowId'], $value['qty']);
        }
        return Cart::content();
    }

    public function remove(string $rowId) {
        Cart::remove($rowId);
        return response()->json(['message' => 'Remove Success']);
    }

    public function removeAll() {
        $cartItems = Cart::content();
        if(count($cartItems) <= 0) {
            return response()->json(['message' => 'Cart is Empty']);
        }
        Cart::destroy();
        return response()->json(['message' => 'Remove All Items Success']);

    }

    public function addCart(Request $request)
    {
        $product = Product::find($request->product_id);
        $cart = Cart::instance('default')->add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price_sale < $product->price ? $product->price_sale : $product->price,
            'qty' => $request->qty,
            'weight' => 0,
            'options' => ['image' => $product->file]
        ]);
        return $cart;
    }

    public function payment(Request $request) {
        dd($request->all());
        $data = $request->toArray();
        return $this->cartService->payment($data);
    }
}
