<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'question', 
        'answer', 
        'order', 
        'is_active',
        'faq_category_id',
    ];

    public function category()
    {
        return $this->belongsTo(FaqCategory::class);
    }
}