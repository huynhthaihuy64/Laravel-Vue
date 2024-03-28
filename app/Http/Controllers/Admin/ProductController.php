<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Services\Product\ProductAdminService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductAdminService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $paginate = $request->limit ?? 5;
        $data = $this->productService->get($request->toArray(),$paginate);
        $listProducts = ProductResource::collection($data);
        Log::channel('history')->info('Người dùng ' . Auth::user()->id . ' đã xóa: ' . "\n" . 'data trước thay đổi: ' . json_encode($data) . "\n" . "data sau thay đổi : đã xóa" . "\n" . "Thời gian thay đổi: " . Carbon::now() . "\n" . "Được thay đổi bởi: " . Auth::user()->name);
        return $listProducts;
    }

    public function relativeProducts(Request $request) {
        $paginate = $request->limit ?? 5;
        $ids = $request->ids;
        $data = $this->productService->getRelativeProduct($paginate, $ids);
        $listProducts = ProductResource::collection($data);
        return $listProducts;
    }

    public function store(Request $request)
    {
        $data = $request->only('name', 'description', 'menu_id', 'price', 'price_sale', 'active', 'file', 'content','images','inventory_number');
        $product = $this->productService->insert($data);
        return $product;
    }

    public function show(int $id)
    {
        $product = $this->productService->show($id);
        return ProductResource::make($product);
    }


    public function update(Request $request, int $id)
    {
        $data = $request->only('name', 'description', 'menu_id', 'price', 'price_sale', 'active', 'file', 'content', 'inventory_number');
        $result = $this->productService->update($data, $id);
        if (!$result) {
            return false;
        }

        return $result;
    }

    public function getProductMenu(Request $request) {
        $paginate = $request->limit ?? 6;
        $data = $this->productService->getProductMenu($request->toArray(),$paginate);
        $listProducts = ProductResource::collection($data);
        return $listProducts;
    }


    public function destroy(int $id)
    {
        $result = $this->productService->delete($id);
        if ($result) {
            return response()->json([
                'error' => false,
                'message' => __('messages.product.delete.success')
            ]);
        }

        return response()->json(['error' => true]);
    }
}
