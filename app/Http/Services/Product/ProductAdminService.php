<?php


namespace App\Http\Services\Product;

use App\Http\Services\UploadService;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Traits\Sortable;

class ProductAdminService
{
    protected $uploadService;

    use Sortable;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    public function getMenu()
    {
        return Menu::where('active', 1)->get();
    }

    protected function isValidPrice($request)
    {
        if (
            (int)$request['price'] != 0 && (int)$request['price_sale'] != 0
            && (int)$request['price_sale'] >= (int)$request['price']
        ) {
            return false;
        }

        if ((int)$request['price'] != 0 && (int)$request['price_sale'] == 0) {
            return false;
        }
        return  true;
    }

    public function insert($request)
    {
        $isValidPrice = $this->isValidPrice($request);
        $file = $this->uploadService->uploadFile($request['file'], 'product');
        $request['file'] = $file['file_path'];
        if ($isValidPrice === false) return false;
        try {
            $product = Product::create([
                'name' => $request['name'],
                'price' => $request['price'],
                'price_sale' => $request['price_sale'],
                'description' => $request['description'],
                'content' => isset($request['content']) ? $request['content'] : null,
                'file' => $request['file'],
                'active' => $request['active'],
            ]);
            $product->menus()->sync($request['menu_id']);
            return $product;
        } catch (\Exception $err) {
            Session::flash('error', 'Add Product Failed');
            Log::info($err->getMessage());
            return  $err;
        }
    }

    public function get($data,$paginate)
    {
        return Product::sort($data)->with('menus')->paginate($paginate);
    }

    public function getRelativeProduct($paginate,array $ids) {
        return Product::with('menus')->whereHas('menus', function($q) use($ids) {
            $q->whereIn('id', $ids);
        })->orderByDesc('id')->paginate($paginate);
    }

    public function show($id)
    {
        return Product::with('menus','comments')->find($id);
    }

    public function update($request, $id)
    {
        $product = Product::findOrFail($id);
        $isValidPrice = $this->isValidPrice($request);
        $file = $this->uploadService->uploadFile($request['file'], 'product');
        $request['file'] = $file['file_path'];
        if ($isValidPrice === false) return false;
        try {
            $product->update([
                'name' => $request['name'],
                'price' => $request['price'],
                'price_sale' => $request['price_sale'],
                'description' => $request['description'],
                'content' => isset($request['content']) ? $request['content'] : null,
                'file' => $request['file'],
                'active' => $request['active'],
            ]);
            $product->menus()->detach();
            $product->menus()->attach($request['menu_id']);
            // foreach($request['menu_id'] as $menu){
            //     if(in_array($menu,$product->menus()->pluck('id')->toArray())){
            //         continue;
            //     } else {
            //         $product->menus()->attach($menu);
            //     }
            // }
            return $product;
        } catch (\Exception $err) {
            Session::flash('error', 'Update Product Failed');
            Log::info($err->getMessage());
            return false;
        }
        return true;
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if ($product) {
            $path = str_replace('storage', 'public', $product->file);
            Storage::delete($path);
            $product->delete();
            return true;
        }

        return false;
    }
}
