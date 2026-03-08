<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NavigationSession;

class NavigationSessionController extends Controller
{
    /**
     * Create a new navigation session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exhibit_id' => 'required|integer',
            'floor_plan_id' => 'sometimes|integer',
            'started_at' => 'sometimes|string',
        ]);

        $session = NavigationSession::create([
            'user_id' => $request->user()->id,
            'exhibit_id' => $validated['exhibit_id'],
            'floor_plan_id' => $validated['floor_plan_id'] ?? null,
            'started_at' => $validated['started_at'] ?? now(),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
            'message' => 'Navigation session started.',
        ]);
    }

    /**
     * Update (end) a navigation session
     */
    public function update(Request $request, $sessionId)
    {
        $session = NavigationSession::where('id', $sessionId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'ended_at' => 'sometimes|string',
            'total_distance' => 'sometimes|numeric',
            'destinations_visited' => 'sometimes|array',
        ]);

        $session->update([
            'ended_at' => $validated['ended_at'] ?? now(),
            'total_distance' => $validated['total_distance'] ?? 0,
            'destinations_visited' => $validated['destinations_visited'] ?? [],
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Navigation session ended.',
            'session' => $session,
        ]);
    }

    /**
     * Log a navigation event
     */
    public function logEvent(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:navigation_sessions,id',
            'event_type' => 'required|string|in:qr_scan,destination_selected,path_started,path_completed,recenter',
            'event_data' => 'sometimes|string',
            'timestamp' => 'sometimes|string',
        ]);

        $session = NavigationSession::where('id', $validated['session_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Append event to session's events JSON
        $events = $session->events ?? [];
        $events[] = [
            'type' => $validated['event_type'],
            'data' => $validated['event_data'] ?? null,
            'timestamp' => $validated['timestamp'] ?? now()->toIso8601String(),
        ];

        $session->update(['events' => $events]);

        return response()->json([
            'success' => true,
            'message' => 'Event logged.',
        ]);
    }

    /**
     * List navigation sessions (admin)
     */
    public function index(Request $request)
    {
        $query = NavigationSession::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by exhibit
        if ($request->has('exhibit_id')) {
            $query->where('exhibit_id', $request->exhibit_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from')) {
            $query->where('started_at', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->where('started_at', '<=', $request->to);
        }

        $sessions = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }

    /**
     * Get aggregated navigation statistics
     */
    public function stats(Request $request)
    {
        $query = NavigationSession::query();

        if ($request->has('exhibit_id')) {
            $query->where('exhibit_id', $request->exhibit_id);
        }

        $period = $request->input('period', 'week');
        $fromDate = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        };

        $query->where('started_at', '>=', $fromDate);

        $totalSessions = $query->count();
        $completedSessions = (clone $query)->where('status', 'completed')->count();
        $uniqueUsers = (clone $query)->distinct('user_id')->count('user_id');
        $avgDistance = (clone $query)->where('total_distance', '>', 0)->avg('total_distance');

        // Most popular destinations
        $allDestinations = NavigationSession::where('started_at', '>=', $fromDate)
            ->whereNotNull('destinations_visited')
            ->pluck('destinations_visited')
            ->flatten()
            ->countBy()
            ->sortDesc()
            ->take(10);

        return response()->json([
            'success' => true,
            'stats' => [
                'total_sessions' => $totalSessions,
                'completed_sessions' => $completedSessions,
                'unique_users' => $uniqueUsers,
                'avg_distance' => round($avgDistance ?? 0, 2),
                'popular_destinations' => $allDestinations,
                'period' => $period,
            ],
        ]);
    }
}
