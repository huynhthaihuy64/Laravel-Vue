<?php


namespace App\Http\Services\Product;

use App\Models\Menu;
use App\Models\Product;
use Carbon\Carbon;

class ProductService
{
    protected $menu;
    protected $product;

    public function __construct(Menu $menu, Product $product)
    {
        $this->menu = $menu;
        $this->product = $product;
    }
    const LIMIT = 1;

    public function get($page = null)
    {
        return $this->product->select('id', 'name', 'price', 'price_sale', 'file')
            ->orderByDesc('id')
            ->when($page != null, function ($query) use ($page) {
                $query->offset($page * self::LIMIT);
            })
            ->limit(self::LIMIT)
            ->paginate(4);
    }

    public function getAll()
    {
        return $this->product->all();
    }

    
    public function show($id)
    {
        return $this->product->where('id', $id)
            ->where('active', 1)
            ->with('menu')
            ->firstOrFail();
    }

    public function more($id)
    {
        return $this->product->select('id', 'name', 'price', 'price_sale', 'file')
            ->where('active', 1)
            ->where('id', '!=', $id)
            ->orderByDesc('id')
            ->limit(8)
            ->get();
    }

    public function search($data) {
        $textSearch = $data['name'] ?? null;
        $products = $this->product->when(isset($textSearch), function($q) use($textSearch) {
            $q->where('name', 'LIKE', "%$textSearch%");
        })->get();
        return $products;
    }

    /**
     * Handle processing of performance data
     *
     * @param array $item
     * @param int $type
     * @param string $time
     * @param int $userId
     */
    public function handleProductData($item, $time): void
    {
        $common = [
            'name' => $item['name'],
            'description' => $item['description'],
            'content' => $item['content'],
            'price' => $item['price'],
            'price_sale' => $item['price_sale'],
            'active' => $item['active'],
            'updated_at' => $time
        ];
        $menu = $this->menu->where('name', $item['menu'])->first()?->toArray();
        $product = $this->product->updateOrCreate(['name' => $item['name']], $common);
        $product->menus()->sync($menu['id']);
    }

    public function updateListCurrency() 
    {
        $key = env('EXCHANGE_CURRENCY');
        $url = "https://api.currencyapi.com/v3/historical?apikey=" . $key . '&date=' . Carbon::now()->subDay()->format('Y-m-d');
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        $json = json_decode($response, true);

        $jsonFilePath = storage_path('setting.json');
        $jsonData = file_get_contents($jsonFilePath);
        $data = json_decode($jsonData, true);
        $data = $json['data'];
        $newJsonData = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($jsonFilePath, $newJsonData);
        return true;
    }
}
