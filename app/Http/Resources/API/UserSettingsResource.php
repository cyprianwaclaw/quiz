<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSettingsResource extends JsonResource
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
            'personal' => [
                'name' => $this->name,
                'surname' => $this->surname,
                'email' => $this->email,
                'phone' => $this->phone,
            ],
            'company' => [
                'name' => $this->company->name ?? '',
                'nip' => $this->company->nip ?? '',
                'regon' => $this->company->regon ?? '',
                'address' => [
                    'city' => $this->company->address->city ?? '',
                    'postcode' => $this->company->address->postcode ?? '',
                    'street' => $this->company->address->street ?? '',
                    'building_number' => $this->company->address->building_number ?? '',
                    'house_number' => $this->company->address->house_number ?? '',
                ],
            ],
            'financial' => [
                'bank_name' => $this->financial->bank_name ?? '',
                'iban' => $this->financial->iban ?? '',
                'swift' => $this->financial->swift ?? '',
            ]
        ];
    }
}
