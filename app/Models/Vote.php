<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'project_id',
        'voter_email',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}