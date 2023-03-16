<?php


namespace App\Http\Services\Product;


use App\Models\Product;

class ProductService
{
    const LIMIT = 1;

    public function get($page = null)
    {
        return Product::select('id', 'name', 'price', 'price_sale', 'file')
            ->orderByDesc('id')
            ->when($page != null, function ($query) use ($page) {
                $query->offset($page * self::LIMIT);
            })
            ->limit(self::LIMIT)
            ->paginate(4);
        // ->get();
    }

    public function getByMenu($id)
    {
        if($id == 0){
            return Product::orderBy('created_at', 'desc')->paginate(4);
        }
        return Product::where('menu_id', $id)->orderBy('created_at', 'desc')->paginate(4);
    }

    public function getAll()
    {
        return Product::all();
    }

    
    public function show($id)
    {
        return Product::where('id', $id)
            ->where('active', 1)
            ->with('menu')
            ->firstOrFail();
    }

    public function more($id)
    {
        return Product::select('id', 'name', 'price', 'price_sale', 'file')
            ->where('active', 1)
            ->where('id', '!=', $id)
            ->orderByDesc('id')
            ->limit(8)
            ->get();
    }
}
