<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;
    protected $fillable = [
        'first_name', 
        'last_name', 
        'email', 
        'phone', 
        'password', 
        'is_active'
    ];

    protected $hidden = ['password'];

    public static function generateRandomPassword($length = 12)
    {
        return Str::random($length);
    }
}