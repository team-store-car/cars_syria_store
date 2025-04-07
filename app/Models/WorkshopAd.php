<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkshopAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'title',
        'description',
        'price',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(WorkshopAdImage::class);
    }
}
