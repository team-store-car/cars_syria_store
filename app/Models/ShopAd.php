<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'title',
        'description',
        'price',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ShopAdImage::class);
    }
}
