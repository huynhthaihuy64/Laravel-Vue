<?php

namespace App\Http\Services\Menu;

use App\Models\Menu;

class MenuService
{

    public function getAll($paginate)
    {
        return Menu::orderbyDesc('id')->paginate($paginate);
    }

    public function show($id)
    {
        return Menu::find($id);
    }

    public function getParent()
    {
        return Menu::where('parent_id', 0)->get();
    }

    public function create($data)
    {
        try {
            $menu = Menu::create([
                'name' => $data['name'],
                'parent_id' => $data['parent_id'],
                'description' => $data['description'],
                'content' => $data['content'],
                'active' => $data['active'],
            ]);

            return $menu;
        } catch (\Exception $err) {
            session()->flash('error', $err->getMessage());
            return false;
        }
    }

    public function update($data, $id)
    {
        try {
            $menu = Menu::find($id);
            $menu->update($data);
            return $menu;
        } catch (\Exception $err) {
            session()->flash('error', $err->getMessage());
            return false;
        }
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            return $menu->delete();
        }
        return false;
    }

    public function getId($id)
    {
        return Menu::where('id', $id)->where('parent_id', 0)->firstOrFail();
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
}
