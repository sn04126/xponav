<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\ExhibitFloorPlan;
use App\Models\HeatMapAggregate;
use App\Models\PositionTrack;
use Illuminate\Http\Request;

class HeatMapController extends Controller
{
    /**
     * Display the heat map dashboard
     */
    public function index(Request $request)
    {
        $exhibits = Exhibit::with('floorPlans')->where('is_active', true)->get();
        $selectedExhibitId = $request->input('exhibit_id', $exhibits->first()->id ?? null);
        $selectedFloorPlanId = $request->input('floor_plan_id');
        $period = $request->input('period', 'month');

        $selectedExhibit = null;
        $floorPlans = collect();
        $heatMapData = null;

        if ($selectedExhibitId) {
            $selectedExhibit = Exhibit::with('floorPlans')->find($selectedExhibitId);
            $floorPlans = $selectedExhibit->floorPlans ?? collect();

            if (!$selectedFloorPlanId && $floorPlans->isNotEmpty()) {
                $selectedFloorPlanId = $floorPlans->first()->id;
            }
        }

        if ($selectedExhibitId && $selectedFloorPlanId) {
            $heatMapData = $this->getHeatMapDataForView($selectedExhibitId, $selectedFloorPlanId, $period);
        }

        return view('admin.heat-maps.index', compact(
            'exhibits',
            'selectedExhibitId',
            'selectedFloorPlanId',
            'selectedExhibit',
            'floorPlans',
            'period',
            'heatMapData'
        ));
    }

    /**
     * Get heat map data for a specific floor plan (AJAX)
     */
    public function getData(Request $request)
    {
        $exhibitId = $request->input('exhibit_id');
        $floorPlanId = $request->input('floor_plan_id');
        $period = $request->input('period', 'month');

        if (!$exhibitId || !$floorPlanId) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $data = $this->getHeatMapDataForView($exhibitId, $floorPlanId, $period);

        return response()->json($data);
    }

    /**
     * Get heat map data formatted for view
     */
    private function getHeatMapDataForView($exhibitId, $floorPlanId, $period)
    {
        $floorPlan = ExhibitFloorPlan::find($floorPlanId);

        // Get aggregated heat map data
        $aggregates = HeatMapAggregate::forExhibit($exhibitId)
            ->forFloorPlan($floorPlanId)
            ->forPeriod($period)
            ->selectRaw('
                grid_x,
                grid_z,
                SUM(visit_count) as total_visits,
                SUM(unique_visitors) as unique_visitors
            ')
            ->groupBy('grid_x', 'grid_z')
            ->get();

        $maxVisits = $aggregates->max('total_visits') ?: 1;

        // Transform to heat map points
        $points = $aggregates->map(function ($item) use ($maxVisits, $floorPlan) {
            // Normalize coordinates to percentage for CSS positioning
            $percentX = $floorPlan ? ($item->grid_x / ($floorPlan->width ?: 50)) * 100 : 50;
            $percentZ = $floorPlan ? ($item->grid_z / ($floorPlan->length ?: 40)) * 100 : 50;

            return [
                'x' => $item->grid_x,
                'z' => $item->grid_z,
                'percent_x' => min(100, max(0, $percentX)),
                'percent_z' => min(100, max(0, $percentZ)),
                'visits' => $item->total_visits,
                'unique_visitors' => $item->unique_visitors,
                'intensity' => $item->total_visits / $maxVisits,
            ];
        })->values();

        // Calculate statistics
        $totalVisits = $aggregates->sum('total_visits');
        $uniqueVisitors = $aggregates->sum('unique_visitors');

        // Get top areas
        $topAreas = $aggregates->sortByDesc('total_visits')->take(5)->values();

        // Get daily trend data
        $dailyTrend = HeatMapAggregate::forExhibit($exhibitId)
            ->forFloorPlan($floorPlanId)
            ->forPeriod($period)
            ->selectRaw('aggregation_date, SUM(visit_count) as visits')
            ->groupBy('aggregation_date')
            ->orderBy('aggregation_date')
            ->get();

        return [
            'floor_plan' => $floorPlan,
            'points' => $points,
            'total_visits' => $totalVisits,
            'unique_visitors' => $uniqueVisitors,
            'max_visits' => $maxVisits,
            'top_areas' => $topAreas,
            'daily_trend' => $dailyTrend,
            'bounds' => [
                'width' => $floorPlan->width ?? 50,
                'length' => $floorPlan->length ?? 40,
            ],
        ];
    }

    /**
     * Trigger manual aggregation
     */
    public function aggregate(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $exhibitId = $request->input('exhibit_id');
        $gridSize = $request->input('grid_size', 1.0);

        if ($exhibitId) {
            $exhibit = Exhibit::with('floorPlans')->find($exhibitId);
            foreach ($exhibit->floorPlans as $floorPlan) {
                HeatMapAggregate::aggregateFromTracks($exhibitId, $floorPlan->id, $date, $gridSize);
            }
        } else {
            // Aggregate all exhibits
            $exhibits = Exhibit::with('floorPlans')->get();
            foreach ($exhibits as $exhibit) {
                foreach ($exhibit->floorPlans as $floorPlan) {
                    HeatMapAggregate::aggregateFromTracks($exhibit->id, $floorPlan->id, $date, $gridSize);
                }
            }
        }

        return redirect()->back()->with('success', 'Heat map data aggregated successfully');
    }

    /**
     * Export heat map data as CSV
     */
    public function export(Request $request)
    {
        $exhibitId = $request->input('exhibit_id');
        $floorPlanId = $request->input('floor_plan_id');
        $period = $request->input('period', 'month');

        $data = HeatMapAggregate::forExhibit($exhibitId)
            ->forFloorPlan($floorPlanId)
            ->forPeriod($period)
            ->with(['exhibit', 'floorPlan'])
            ->get();

        $csv = "Date,Exhibit,Floor,Grid X,Grid Z,Visit Count,Unique Visitors\n";

        foreach ($data as $row) {
            $csv .= sprintf(
                "%s,%s,%s,%.2f,%.2f,%d,%d\n",
                $row->aggregation_date->format('Y-m-d'),
                $row->exhibit->name ?? '',
                $row->floorPlan->name ?? '',
                $row->grid_x,
                $row->grid_z,
                $row->visit_count,
                $row->unique_visitors
            );
        }

        $filename = "heatmap_export_" . date('Y-m-d_His') . ".csv";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
