<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'department' => $this->department,
            'birthday' => Carbon::parse($this->birthday)->format('Y/m/d'),
            'gender' => $this->gender === 0 ? __('messages.user.gender.male') : __('messages.user.gender.female'),
            'role_id' => $this->role_id,
            'address' => $this->address,
            'created_at' => Carbon::parse($this->created_at)->format('Y/m/d'),
            'images' => UserAlbums::collection($this->userAlbums)
          ];
    }
}
