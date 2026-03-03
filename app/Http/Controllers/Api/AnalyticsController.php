<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\ExhibitVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Log a visit when user arrives at an exhibit via AR navigation
     */
    public function logVisit(Request $request, Exhibit $exhibit)
    {
        $validated = $request->validate([
            'duration_seconds' => 'nullable|integer|min:0',
            'source' => 'nullable|string|in:ar_navigation,manual,qr_scan',
            'path_data' => 'nullable|array',
            'start_anchor' => 'nullable|string',
            'end_anchor' => 'nullable|string',
            'floor_level' => 'nullable|integer',
        ]);

        $visit = ExhibitVisit::create([
            'user_id' => auth()->id(),
            'exhibit_id' => $exhibit->id,
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
            'source' => $validated['source'] ?? 'ar_navigation',
            'path_data' => $validated['path_data'] ?? null,
            'start_anchor' => $validated['start_anchor'] ?? null,
            'end_anchor' => $validated['end_anchor'] ?? null,
            'floor_level' => $validated['floor_level'] ?? null,
            'visit_date' => now()->toDateString(),
            'visit_time' => now()->toTimeString(),
        ]);

        // Also increment the view_count on the exhibit
        $exhibit->increment('view_count');

        return response()->json([
            'success' => true,
            'message' => 'Visit logged successfully',
            'data' => $visit
        ]);
    }

    /**
     * Get most visited exhibits
     */
    public function mostVisited(Request $request)
    {
        $period = $request->get('period', 'all'); // all, today, week, month
        $limit = $request->get('limit', 10);

        $query = ExhibitVisit::select('exhibit_id', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('exhibit_id')
            ->orderByDesc('visit_count')
            ->limit($limit);

        // Apply period filter
        switch ($period) {
            case 'today':
                $query->today();
                break;
            case 'week':
                $query->thisWeek();
                break;
            case 'month':
                $query->thisMonth();
                break;
        }

        $visitCounts = $query->get();

        // Get exhibit details
        $exhibitIds = $visitCounts->pluck('exhibit_id');
        $exhibits = Exhibit::whereIn('id', $exhibitIds)->get()->keyBy('id');

        $result = $visitCounts->map(function ($item) use ($exhibits) {
            $exhibit = $exhibits->get($item->exhibit_id);
            return [
                'exhibit_id' => $item->exhibit_id,
                'visit_count' => $item->visit_count,
                'exhibit' => $exhibit ? [
                    'id' => $exhibit->id,
                    'name' => $exhibit->name,
                    'description' => $exhibit->description,
                    'image' => $exhibit->image,
                    'location' => $exhibit->location,
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => $result
        ]);
    }

    /**
     * Get analytics dashboard data for admin
     */
    public function dashboard(Request $request)
    {
        // Total visits
        $totalVisits = ExhibitVisit::count();
        $todayVisits = ExhibitVisit::today()->count();
        $weekVisits = ExhibitVisit::thisWeek()->count();
        $monthVisits = ExhibitVisit::thisMonth()->count();

        // Average duration
        $avgDuration = ExhibitVisit::avg('duration_seconds') ?? 0;

        // Visits by source
        $visitsBySource = ExhibitVisit::select('source', DB::raw('COUNT(*) as count'))
            ->groupBy('source')
            ->get();

        // Visits by hour (for today)
        $visitsByHour = ExhibitVisit::today()
            ->select(DB::raw('HOUR(visit_time) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('HOUR(visit_time)'))
            ->orderBy('hour')
            ->get();

        // Top 5 exhibits this week
        $topExhibits = ExhibitVisit::thisWeek()
            ->select('exhibit_id', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('exhibit_id')
            ->orderByDesc('visit_count')
            ->limit(5)
            ->with('exhibit:id,name')
            ->get();

        // Daily visits for last 7 days
        $dailyVisits = ExhibitVisit::where('visit_date', '>=', now()->subDays(7)->toDateString())
            ->select('visit_date', DB::raw('COUNT(*) as count'))
            ->groupBy('visit_date')
            ->orderBy('visit_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_visits' => $totalVisits,
                    'today_visits' => $todayVisits,
                    'week_visits' => $weekVisits,
                    'month_visits' => $monthVisits,
                    'avg_duration_seconds' => round($avgDuration),
                ],
                'visits_by_source' => $visitsBySource,
                'visits_by_hour' => $visitsByHour,
                'top_exhibits' => $topExhibits,
                'daily_visits' => $dailyVisits,
            ]
        ]);
    }

    /**
     * Get user's visit history
     */
    public function userHistory(Request $request)
    {
        $visits = ExhibitVisit::where('user_id', auth()->id())
            ->with('exhibit:id,name,image,location')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $visits
        ]);
    }

    /**
     * Get detailed analytics for a specific exhibit
     */
    public function exhibitAnalytics(Exhibit $exhibit)
    {
        $totalVisits = ExhibitVisit::where('exhibit_id', $exhibit->id)->count();
        $uniqueVisitors = ExhibitVisit::where('exhibit_id', $exhibit->id)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count();
        $avgDuration = ExhibitVisit::where('exhibit_id', $exhibit->id)->avg('duration_seconds') ?? 0;

        // Visits trend (last 30 days)
        $visitsTrend = ExhibitVisit::where('exhibit_id', $exhibit->id)
            ->where('visit_date', '>=', now()->subDays(30)->toDateString())
            ->select('visit_date', DB::raw('COUNT(*) as count'))
            ->groupBy('visit_date')
            ->orderBy('visit_date')
            ->get();

        // Peak hours
        $peakHours = ExhibitVisit::where('exhibit_id', $exhibit->id)
            ->select(DB::raw('HOUR(visit_time) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('HOUR(visit_time)'))
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'exhibit' => [
                    'id' => $exhibit->id,
                    'name' => $exhibit->name,
                ],
                'total_visits' => $totalVisits,
                'unique_visitors' => $uniqueVisitors,
                'avg_duration_seconds' => round($avgDuration),
                'visits_trend' => $visitsTrend,
                'peak_hours' => $peakHours,
            ]
        ]);
    }
}
