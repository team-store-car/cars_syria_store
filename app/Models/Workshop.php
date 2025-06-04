<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // استيراد العلاقة
use Illuminate\Database\Eloquent\Relations\BelongsTo; // استيراد العلاقة
use Illuminate\Notifications\Notifiable;

class Workshop extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'location',
        'description',
        'city',
        'user_id',
        'commercial_registration_number',
        'commercial_registration_image',
        'verified',
        'certification_details',

    ];

    /**
     * الحصول على المستخدم الذي يملك ورشة العمل.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

 
    public function workshopAds(): HasMany // <--- تأكد من هذا السطر
    {
        return $this->hasMany(WorkshopAd::class);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail(): string
    {
        return $this->user->email;
    }
}