<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'car_id' => $this->car_id,
            'offer_type' => $this->offer_type,
            'price' => $this->price,
            'location' => $this->location,
            'price_unit' => $this->price_unit,
            'pricing_period' => $this->pricing_period,
            'is_available' => $this->is_available,
            'additional_features' => $this->additional_features,
            'car' => new CarResource($this->whenLoaded('car')),
        ];
    }
}
