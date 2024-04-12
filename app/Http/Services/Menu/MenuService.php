<?php

namespace App\Http\Services\Menu;

use App\Models\Menu;
use App\Traits\Sortable;
use Illuminate\Support\Facades\DB;

class MenuService
{
    protected $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    use Sortable;
    
    public function getAll($data,$paginate)
    {
        return $this->menu->sort($data)->paginate($paginate);
    }

    public function show($id)
    {
        return $this->menu->find($id);
    }

    public function getParent()
    {
        return $this->menu->where('parent_id', 0)->get();
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            $menu = $this->menu->create([
                'name' => $data['name'],
                'parent_id' => $data['parent_id'],
                'description' => $data['description'],
                'content' => $data['content'],
                'sub_id' => $data['sub_id'] ?? 0,
                'active' => $data['active'],
            ]);
            DB::commit();
            return $menu;
        } catch (\Exception $err) {
            DB::rollback();
            return $err->getMessage();
        }
    }

    public function update($data, $id)
    {
        try {
            $menu = $this->menu->find($id);
            $menu->update($data);
            return $menu;
        } catch (\Exception $err) {
            session()->flash('error', $err->getMessage());
            return false;
        }
    }

    public function destroy($id)
    {
        $menu = $this->menu->find($id);
        if ($menu) {
            return $menu->delete();
        }
        return false;
    }

    public function getId($id)
    {
        return $this->menu->where('id', $id)->where('parent_id', 0)->firstOrFail();
    }

    public function getProduct($menu, $request)
    {
        $query = $menu->products()->select('id', 'name', 'price', 'price_sale', 'file')->where('active', 1);

        if ($request->input('price_sale')) {
            $query->orderBy('price_sale', $request->input('price_sale'));
        }
        return $query->orderByDesc('id')
            ->paginate(12)->withQueryString();
    }

    public function getProductsWithMenu($id)
    {
        $menu = $this->menu->find($id);
        $products = $menu->products()->get();
        return $products;
    }
}
