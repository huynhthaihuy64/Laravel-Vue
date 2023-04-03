<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'phone' => $this->phone,
            'address' => $this->address,
            'email' => $this->email,
            'content' => $this->content,
            'total_tax' => $this->total_tax,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'cart' => CartResource::collection($this->carts),
          ];
    }
}
