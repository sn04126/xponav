<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExhibitVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exhibit_id',
        'duration_seconds',
        'source',
        'path_data',
        'start_anchor',
        'end_anchor',
        'floor_level',
        'visit_date',
        'visit_time',
    ];

    protected $casts = [
        'path_data' => 'array',
        'visit_date' => 'date',
        'visit_time' => 'datetime',
    ];

    /**
     * Get the user who made this visit
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exhibit that was visited
     */
    public function exhibit()
    {
        return $this->belongsTo(Exhibit::class);
    }

    /**
     * Scope for visits within date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('visit_date', [$startDate, $endDate]);
    }

    /**
     * Scope for today's visits
     */
    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', now()->toDateString());
    }

    /**
     * Scope for this week's visits
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('visit_date', [
            now()->startOfWeek()->toDateString(),
            now()->endOfWeek()->toDateString()
        ]);
    }

    /**
     * Scope for this month's visits
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visit_date', now()->month)
                     ->whereYear('visit_date', now()->year);
    }
}
