<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $replies = [];
        foreach($this->replies as $reply) {
            $replies[] = [
                'id' => $reply->id,
                'user_id' => $reply->user_id,
                'parent_id' => $reply->parent_id,
                'product_id' => $reply->product_id,
                'body' => $reply->body,
                'created_at' => Carbon::parse($reply->created_at)->format('d/m/Y'),
                'replies' => $reply->replies,
                'user' => $reply->user,
            ];
        }
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'product_id' => $this->product_id,
            'body' => $this->body,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'replies' => $replies,
            'user' => $this->user,
          ];
    }
}
