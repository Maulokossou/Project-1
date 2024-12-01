<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id', 
        'type', 
        'description', 
        'ip_address'
    ];

    public function client()
    {
        return $this->belongsTo(Customer::class);
    }
}