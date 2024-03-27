<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $menuName = '';
        if ($this->menus) { 
        $menuName = implode(", ", array_map(function ($value) {
            return $value['name'];
        }, $this->menus->toArray()));
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'price_sale' => $this->price_sale,
            'active' => $this->active,
            'file' => $this->file,
            'menus' => $this->menus,
            'menuName' => $menuName,
            'content' => $this->content,
            'comments' => $this->comments()->get(),
            'images' => $this->images,
            'price_currency' => $this->price_currency,
            'price_sale_currency' => $this->price_sale_currency,
            'inventory_number' => $this->inventory_number,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
        ];
    }
}
