<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($car) {
    //         $car->slug = Str::slug($car->name) . '-' . Str::random(6);
    //     });

    //     static::updating(function ($car) {
    //         if ($car->isDirty('name')) {
    //             $car->slug = Str::slug($car->name) . '-' . Str::random(6);
    //         }
    //     });
    // }
}
