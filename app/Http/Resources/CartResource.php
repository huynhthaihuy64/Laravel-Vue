<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $jsonFilePath = storage_path('setting.json');
        $jsonData = file_get_contents($jsonFilePath);
        $data = json_decode($jsonData, true);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'qty' => $this->qty,
            'product' => $this->product,
            'price' => $this->price,
            'total' => round($data[Auth::user()->currency]['value'] * $this->total,2),
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y')
          ];
    }
}
