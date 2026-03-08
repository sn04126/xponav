<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationSession extends Model
{
    protected $fillable = [
        'user_id',
        'exhibit_id',
        'floor_plan_id',
        'started_at',
        'ended_at',
        'total_distance',
        'destinations_visited',
        'events',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'destinations_visited' => 'array',
        'events' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exhibit()
    {
        return $this->belongsTo(Exhibit::class);
    }
}
