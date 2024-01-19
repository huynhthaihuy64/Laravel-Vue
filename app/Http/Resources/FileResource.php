<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'file_name' => $this->file_name,
            'file_path'  => $this->file_path,
            'status'  => $this->status,
            'type'  => $this->type,
            'file_type'  => $this->file_type,
            'created_at' => $this->created_at,
            'user' => $this->user,
        ];

        return $fields;
    }
}
