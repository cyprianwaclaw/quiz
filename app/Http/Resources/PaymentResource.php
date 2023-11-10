<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'title' => $this->planSubscription->plan->name,
            'purchaseDate' => $this->planSubscription->starts_at->format('d-m-Y H:i:s'),
            'expiryDate' => $this->planSubscription->ends_at->format('d-m-Y H:i:s'),
            'invoice_id' => $this->ifirma_invoice_id,
        ];
    }
}
