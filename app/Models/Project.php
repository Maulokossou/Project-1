<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'goal_amount',
        'current_amount',
        'start_date',
        'end_date',
        'status',
        "image_url",
        'video_url',
        'association_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'goal_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }
}