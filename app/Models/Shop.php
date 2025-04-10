<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'address', 'description', 'verified','commercial_registration_number',
    'commercial_registration_image'
];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shopAds(): HasMany
    {
        return $this->hasMany(ShopAd::class);
    }

    public function warrantyRequests(): HasMany
    {
        return $this->hasMany(WarrantyRequest::class);
    }
}
