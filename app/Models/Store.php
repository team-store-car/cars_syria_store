<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'description',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'status'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
