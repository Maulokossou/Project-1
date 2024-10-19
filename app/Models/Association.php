<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Association extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'field_of_activity',
        'email',
        'password',
        'is_active',
        'otp',
        'otp_expires_at',
        'last_otp_attempt',
        'otp_attempts',
    ];

    protected $hidden = [
        'password',
        'otp',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'otp_expires_at' => 'datetime',
        'last_otp_attempt' => 'datetime',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}