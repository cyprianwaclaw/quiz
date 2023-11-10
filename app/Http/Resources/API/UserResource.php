<?php

namespace App\Http\Resources\API;

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
            "id" => $this->id,
            "name" => $this->name,
            "surname" => $this->surname,
            "phone" => $this->phone,
            "email" => $this->email,
            "email_verified_at" => $this->email_verified_at,
            "invited_by" => $this->invited_by,
            "points" => $this->points,
            "avatar_path" => $this->avatar_path ? url('storage/user-avatar/'.$this->avatar_path): $this->avatar_path,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            ];
    }
}
