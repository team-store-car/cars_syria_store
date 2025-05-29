<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopAdImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_ad_id',
        'image_path',
    ];

    public function workshopAd(): BelongsTo
    {
        return $this->belongsTo(WorkshopAd::class);
    }
}
