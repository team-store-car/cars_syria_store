<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\InspectionRequest;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Authenticatable
{
    use HasRoles , HasApiTokens, HasFactory;

    protected $fillable = ['name', 'email', 'password','role','phone','avatar'];

    public function workshop()
    {
        return $this->hasOne(Workshop::class, 'user_id');
    }
    public function inspectionRequest()
    {
    return $this->hasOne(InspectionRequest::class);
    }

}
