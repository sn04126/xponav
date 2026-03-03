<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exhibit_id',
        'floor_plan_id',
        'position_x',
        'position_y',
        'position_z',
        'session_id',
        'tracked_at',
    ];

    protected $casts = [
        'position_x' => 'float',
        'position_y' => 'float',
        'position_z' => 'float',
        'tracked_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exhibit()
    {
        return $this->belongsTo(Exhibit::class);
    }

    public function floorPlan()
    {
        return $this->belongsTo(ExhibitFloorPlan::class, 'floor_plan_id');
    }

    // Scopes
    public function scopeForExhibit($query, $exhibitId)
    {
        return $query->where('exhibit_id', $exhibitId);
    }

    public function scopeForFloorPlan($query, $floorPlanId)
    {
        return $query->where('floor_plan_id', $floorPlanId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tracked_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('tracked_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tracked_at', now()->month)
                     ->whereYear('tracked_at', now()->year);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tracked_at', [$startDate, $endDate]);
    }
}
