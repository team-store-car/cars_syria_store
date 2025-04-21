<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'category_id',
        'model',
        'year',
        'country_of_manufacture',
        'condition',
        'mileage',
        'fuel_type',
        'transmission',
        'horsepower',
        'seats',
        'color',
        'description',
        'is_featured',
        'other_benefits',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}