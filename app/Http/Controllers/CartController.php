<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use App\Http\Services\CartService;
use App\Http\Services\Product\ProductService;

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

    public function index(Request $request)
    {
        $data = $this->cartService->index($request->all());
        $result = [
            'carts' => CartResource::collection($data['carts']),
            'total_all' => $data['total_all']
        ];
        return $result;
    }

    public function show($id) {
        return $this->cartService->show($id);
    }

    public function update(Request $request) {
        return $this->cartService->update($request->only(['carts']));
    }

    public function remove($id) {
        return $this->cartService->destroy($id);
    }

    public function addCart(Request $request)
    {
        return $this->cartService->store($request->all());
    }

    public function payment(Request $request) {
        $data = $request->toArray();
        return $this->cartService->payment($data);
    }

    public function success(Request $request)
    {
        return $this->cartService->success($request);
    }
}
