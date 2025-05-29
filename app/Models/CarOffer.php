<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarOffer extends Model
{
    use HasFactory;

    protected $table = 'car_offers';

    protected $fillable = [
        'car_id',
        'offer_type',
        'price',
        'price_unit',
        'location',
        'pricing_period',
        'is_available',
        'additional_features',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
