<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            'name' => $this->name,
            'brand' => $this->brand,
            'category' => new CategoryResource($this->category),
            'offer' => $this->when(
                !$request->routeIs('car-offers.*'),
                fn() => $this->offer ? new CarOfferResource($this->offer) : null
            ),
            'country_of_manufacture' => $this->country_of_manufacture,
            'model' => $this->model,
            'year' => $this->year,
            'condition' => $this->condition,
            'mileage' => $this->mileage,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'horsepower' => $this->horsepower,
            'seats' => $this->seats,
            'color' => $this->color,
            'description' => $this->description,
            'is_featured' => (bool) $this->is_featured,
            'other_benefits' => $this->other_benefits,
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('storage/' . $image->path),
                    'alt_text' => $image->alt_text,
                    'is_primary' => $image->is_primary,
                ];
            }),

            'owner' => ($this->user && $this->user->role == 'shop_manager' && $this->user->store)
            ? [
                'store_id' => $this->user->store->id,
                'store_name' => $this->user->store->name,
                'store_email' => $this->user->store->email,
                'store_logo' => $this->user->store->logo() ? asset('storage/' . $this->user->store->logo()->path) : null,
            ]
            : ($this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ] : null),
        ];
    }
}
