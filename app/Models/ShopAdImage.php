<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopAdImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_ad_id',
        'image_path',
    ];

    public function shopAd(): BelongsTo
    {
        return $this->belongsTo(ShopAd::class);
    }
}
