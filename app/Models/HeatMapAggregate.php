<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeatMapAggregate extends Model
{
    use HasFactory;

    protected $fillable = [
        'exhibit_id',
        'floor_plan_id',
        'grid_x',
        'grid_z',
        'grid_size',
        'visit_count',
        'unique_visitors',
        'aggregation_date',
    ];

    protected $casts = [
        'grid_x' => 'float',
        'grid_z' => 'float',
        'grid_size' => 'float',
        'visit_count' => 'integer',
        'unique_visitors' => 'integer',
        'aggregation_date' => 'date',
    ];

    // Relationships
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

    public function scopeForPeriod($query, $period)
    {
        switch ($period) {
            case 'today':
                return $query->where('aggregation_date', today());
            case 'week':
                return $query->whereBetween('aggregation_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
            case 'month':
                return $query->whereBetween('aggregation_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]);
            case 'year':
                return $query->whereYear('aggregation_date', now()->year);
            default:
                return $query->where('aggregation_date', '>=', now()->subDays(30)->toDateString());
        }
    }

    /**
     * Get aggregated heat map data for a floor plan
     */
    public static function getHeatMapData($exhibitId, $floorPlanId, $period = 'month')
    {
        return self::forExhibit($exhibitId)
            ->forFloorPlan($floorPlanId)
            ->forPeriod($period)
            ->selectRaw('
                grid_x,
                grid_z,
                SUM(visit_count) as total_visits,
                SUM(unique_visitors) as total_unique_visitors
            ')
            ->groupBy('grid_x', 'grid_z')
            ->get();
    }

    /**
     * Aggregate position tracks into heat map data
     */
    public static function aggregateFromTracks($exhibitId, $floorPlanId, $date, $gridSize = 1.0)
    {
        $tracks = PositionTrack::forExhibit($exhibitId)
            ->forFloorPlan($floorPlanId)
            ->whereDate('tracked_at', $date)
            ->get();

        $gridData = [];

        foreach ($tracks as $track) {
            // Calculate grid cell
            $gridX = round($track->position_x / $gridSize) * $gridSize;
            $gridZ = round($track->position_z / $gridSize) * $gridSize;
            $key = "{$gridX}_{$gridZ}";

            if (!isset($gridData[$key])) {
                $gridData[$key] = [
                    'grid_x' => $gridX,
                    'grid_z' => $gridZ,
                    'visit_count' => 0,
                    'unique_users' => [],
                ];
            }

            $gridData[$key]['visit_count']++;

            if ($track->user_id) {
                $gridData[$key]['unique_users'][$track->user_id] = true;
            }
        }

        // Save aggregated data
        foreach ($gridData as $data) {
            self::updateOrCreate(
                [
                    'exhibit_id' => $exhibitId,
                    'floor_plan_id' => $floorPlanId,
                    'grid_x' => $data['grid_x'],
                    'grid_z' => $data['grid_z'],
                    'aggregation_date' => $date,
                ],
                [
                    'grid_size' => $gridSize,
                    'visit_count' => $data['visit_count'],
                    'unique_visitors' => count($data['unique_users']),
                ]
            );
        }
    }
}
