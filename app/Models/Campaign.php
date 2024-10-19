<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'total_amount',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'campaign_project')->withPivot('allocated_amount')->withTimestamps();
    }
}