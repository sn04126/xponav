<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationSession;
use Illuminate\Http\Request;

class NavigationAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'week');
        $fromDate = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        };

        // Sessions query
        $query = NavigationSession::with('user')
            ->where('started_at', '>=', $fromDate)
            ->orderBy('started_at', 'desc');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $sessions = $query->paginate(20);

        // Stats
        $totalQuery = NavigationSession::where('started_at', '>=', $fromDate);
        $stats = [
            'total_sessions' => (clone $totalQuery)->count(),
            'active_sessions' => NavigationSession::where('status', 'active')->count(),
            'unique_users' => (clone $totalQuery)->distinct('user_id')->count('user_id'),
            'avg_distance' => (clone $totalQuery)->where('total_distance', '>', 0)->avg('total_distance') ?? 0,
        ];

        // Popular destinations
        $allDestinations = NavigationSession::where('started_at', '>=', $fromDate)
            ->whereNotNull('destinations_visited')
            ->pluck('destinations_visited')
            ->flatten()
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->toArray();

        $stats['popular_destinations'] = $allDestinations;

        return view('admin.navigation.index', compact('sessions', 'stats'));
    }
}
