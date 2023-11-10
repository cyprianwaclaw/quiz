<?php

namespace App\Http\Resources\API;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInvitedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $is_premium = $this->planSubscriptions->count()
            ? $this->planSubscriptions->last()->active()
            : false;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_premium' => $is_premium,
            'avatar_path' => $this->avatar_path,


        ];
    }
}
