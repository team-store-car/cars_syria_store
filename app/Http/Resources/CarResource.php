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
        'user_id'=> $this->user_id,
        'brand' => $this->brand,
        'category' => new CategoryResource($this->category),
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
        ];
    }
}
