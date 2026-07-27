<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use Illuminate\Http\Request;
use App\Http\Services\Menu\MenuService;

class MenuController extends Controller
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function store(Request $request)
    {
        $menu = $this->menuService->create($request->toArray());

        return $menu;
    }

    public function index(Request $request)
    {
        $paginate = $request->limit ?? 5;
        $menus = MenuResource::collection($this->menuService->getAll($request->toArray(),$paginate));
        return $menus;
    }

    public function show(int $id)
    {
        $menu = $this->menuService->show($id);
        return $menu;
    }

    public function update(Request $request, int $id)
    {
        $menu = $this->menuService->update($request->toArray(), $id);
        return $menu;
    }

    public function destroy(int $id)
    {
        $result = $this->menuService->destroy($id);

        if ($result) {
            return response()->json([
                'error' => false,
                'message' => __('messages.menu.delete.success')
            ]);
        }
        return response()->json([
            'error' => true,
        ]);
    }

    public function getProductsWithMenu(int $id) {
        $menu = $this->menuService->getProductsWithMenu($id);
        return $menu;
    }
}
