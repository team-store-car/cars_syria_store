<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\InspectionRequest;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\CustomVerifyEmail;


class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'avatar', 'email_verified_at'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function workshop()
    {
        return $this->hasOne(Workshop::class, 'user_id');
    }
    public function store()
    {
        return $this->hasOne(Store::class, 'user_id');
    }
    public function cars()
    {
        return $this->hasMany(Car::class, 'user_id');
    }
    public function inspectionRequest()
    {
    return $this->hasOne(InspectionRequest::class);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}
