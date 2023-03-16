<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\Menu\MenuService;
use App\Http\Services\Product\ProductService;

class MenuController extends Controller
{
    //
    protected $menuService;

    protected $productService;

    public function __construct(MenuService $menuService, ProductService $productService)
    {
        $this->menuService = $menuService;
        $this->productService = $productService;
    }
    public function index(Request $request, $id)
    {
        $menu = $this->menuService->getId($id);

        $products = $this->menuService->getProduct($menu, $request);

        return view('menu', [
            'title' => $menu->name,
            'products' => $products,
            'searchProducts' => $this->productService->getAll(),
            'menus' => $menu
        ]);
    }
}
