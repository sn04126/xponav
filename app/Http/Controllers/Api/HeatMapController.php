<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PositionTrack;
use App\Models\HeatMapAggregate;
use App\Models\Exhibit;
use App\Models\ExhibitFloorPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HeatMapController extends Controller
{
    /**
     * Store position tracking data from Unity app (batch upload)
     */
    public function storePositionTracks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tracks' => 'required|array',
            'tracks.*.exhibitId' => 'required|integer|exists:exhibits,id',
            'tracks.*.floorPlanId' => 'required|integer|exists:exhibit_floor_plans,id',
            'tracks.*.positionX' => 'required|numeric',
            'tracks.*.positionY' => 'required|numeric',
            'tracks.*.positionZ' => 'required|numeric',
            'tracks.*.timestamp' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = auth('sanctum')->id();
        $sessionId = $request->input('session_id', uniqid('session_'));
        $tracksReceived = 0;

        foreach ($request->tracks as $track) {
            PositionTrack::create([
                'user_id' => $userId,
                'exhibit_id' => $track['exhibitId'],
                'floor_plan_id' => $track['floorPlanId'],
                'position_x' => $track['positionX'],
                'position_y' => $track['positionY'],
                'position_z' => $track['positionZ'],
                'session_id' => $sessionId,
                'tracked_at' => isset($track['timestamp']) ? \Carbon\Carbon::parse($track['timestamp']) : now(),
            ]);
            $tracksReceived++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Position tracks stored successfully',
            'tracks_received' => $tracksReceived,
        ]);
    }

    /**
     * Get heat map data for visualization
     */
    public function getHeatMapData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exhibit_id' => 'required|integer|exists:exhibits,id',
            'floor_plan_id' => 'required|integer|exists:exhibit_floor_plans,id',
            'period' => 'in:today,week,month,year,all',
            'grid_size' => 'numeric|min:0.5|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $exhibitId = $request->exhibit_id;
        $floorPlanId = $request->floor_plan_id;
        $period = $request->input('period', 'month');
        $gridSize = $request->input('grid_size', 1.0);

        // Get aggregated data
        $heatMapData = HeatMapAggregate::getHeatMapData($exhibitId, $floorPlanId, $period);

        // Calculate max visits for intensity normalization
        $maxVisits = $heatMapData->max('total_visits') ?: 1;

        // Transform data for response
        $points = $heatMapData->map(function ($item) use ($maxVisits) {
            return [
                'x' => $item->grid_x,
                'y' => 0, // Ground level
                'z' => $item->grid_z,
                'count' => $item->total_visits,
                'intensity' => min(1.0, $item->total_visits / $maxVisits),
            ];
        })->values();

        // Calculate stats
        $totalVisits = $heatMapData->sum('total_visits');
        $uniqueVisitors = $heatMapData->sum('total_unique_visitors');

        // Find hottest and coldest areas
        $hottestArea = $heatMapData->sortByDesc('total_visits')->first();
        $coldestArea = $heatMapData->sortBy('total_visits')->first();

        // Get floor plan for reference
        $floorPlan = ExhibitFloorPlan::find($floorPlanId);

        return response()->json([
            'success' => true,
            'data' => [
                'exhibit_id' => $exhibitId,
                'floor_plan_id' => $floorPlanId,
                'floor_plan_name' => $floorPlan->name ?? '',
                'period' => $period,
                'grid_size' => $gridSize,
                'points' => $points,
                'stats' => [
                    'total_visits' => $totalVisits,
                    'unique_visitors' => $uniqueVisitors,
                    'avg_duration' => 0, // Can be calculated from visit logs
                    'hottest_area' => $hottestArea ? "({$hottestArea->grid_x}, {$hottestArea->grid_z})" : 'N/A',
                    'coldest_area' => $coldestArea ? "({$coldestArea->grid_x}, {$coldestArea->grid_z})" : 'N/A',
                    'max_visits_at_point' => $maxVisits,
                ],
                'bounds' => [
                    'min_x' => $heatMapData->min('grid_x') ?? 0,
                    'max_x' => $heatMapData->max('grid_x') ?? 0,
                    'min_z' => $heatMapData->min('grid_z') ?? 0,
                    'max_z' => $heatMapData->max('grid_z') ?? 0,
                ],
            ],
        ]);
    }

    /**
     * Get heat map summary for all floors of an exhibit
     */
    public function getExhibitHeatMapSummary(Request $request, $exhibitId)
    {
        $exhibit = Exhibit::with('floorPlans')->find($exhibitId);

        if (!$exhibit) {
            return response()->json([
                'success' => false,
                'message' => 'Exhibit not found'
            ], 404);
        }

        $period = $request->input('period', 'month');

        $floorSummaries = [];
        foreach ($exhibit->floorPlans as $floorPlan) {
            $data = HeatMapAggregate::forExhibit($exhibitId)
                ->forFloorPlan($floorPlan->id)
                ->forPeriod($period)
                ->selectRaw('SUM(visit_count) as total_visits, SUM(unique_visitors) as unique_visitors')
                ->first();

            $floorSummaries[] = [
                'floor_plan_id' => $floorPlan->id,
                'floor_name' => $floorPlan->name,
                'floor_level' => $floorPlan->floor_level,
                'total_visits' => $data->total_visits ?? 0,
                'unique_visitors' => $data->unique_visitors ?? 0,
            ];
        }

        // Sort by floor level
        usort($floorSummaries, fn($a, $b) => $a['floor_level'] <=> $b['floor_level']);

        return response()->json([
            'success' => true,
            'data' => [
                'exhibit_id' => $exhibitId,
                'exhibit_name' => $exhibit->name,
                'period' => $period,
                'floors' => $floorSummaries,
                'total_visits' => array_sum(array_column($floorSummaries, 'total_visits')),
                'total_unique_visitors' => array_sum(array_column($floorSummaries, 'unique_visitors')),
            ],
        ]);
    }

    /**
     * Trigger aggregation manually (admin only)
     */
    public function aggregateData(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $exhibitId = $request->input('exhibit_id');
        $floorPlanId = $request->input('floor_plan_id');
        $gridSize = $request->input('grid_size', 1.0);

        if ($exhibitId && $floorPlanId) {
            HeatMapAggregate::aggregateFromTracks($exhibitId, $floorPlanId, $date, $gridSize);
        } else {
            // Aggregate all
            $exhibits = Exhibit::with('floorPlans')->get();
            foreach ($exhibits as $exhibit) {
                foreach ($exhibit->floorPlans as $floorPlan) {
                    HeatMapAggregate::aggregateFromTracks($exhibit->id, $floorPlan->id, $date, $gridSize);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Heat map data aggregated successfully',
        ]);
    }
}
