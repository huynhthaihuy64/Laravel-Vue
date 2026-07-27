<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileS3Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $fields = [
            'path' => $this['path'],
            'name' => $this['basename'],
            'extension'  => $this['extension'],
            'created_at' => $this['timestamp'],
        ];

        return $fields;
    }
}
