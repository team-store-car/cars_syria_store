<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category_type'];

    /**
     * Relationship: A category can have multiple cars.
     */
    // public function cars()
    // {
    //     return $this->hasMany(Car::class);
    // }

    // /**
    //  * Relationship: A category can have multiple spare parts.
    //  */
    // public function spareParts()
    // {
    //     return $this->hasMany(SparePart::class);
    // }

    // /**
    //  * Relationship: A category can have multiple workshops.
    //  */
    // public function workshops()
    // {
    //     return $this->hasMany(Workshop::class);
    // }


}
