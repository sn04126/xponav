<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exhibit;
use App\Models\ExhibitFloorPlan;
use App\Models\ARAnchor;
use App\Models\ExhibitVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if user is admin
            if (Auth::user()->is_admin || Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'You do not have admin access.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function index()
    {
        $stats = [
            'exhibits' => Exhibit::count(),
            'floor_plans' => ExhibitFloorPlan::count(),
            'ar_anchors' => ARAnchor::count(),
            'users' => User::count(),
        ];

        // Visit Statistics
        $visit_stats = [
            'total_visits' => ExhibitVisit::count(),
            'today_visits' => ExhibitVisit::whereDate('visit_date', now()->toDateString())->count(),
            'week_visits' => ExhibitVisit::whereBetween('visit_date', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString()
            ])->count(),
            'avg_duration' => ExhibitVisit::avg('duration_seconds') ?? 0,
        ];

        // Most visited exhibits this week
        $most_visited = ExhibitVisit::select('exhibit_id', DB::raw('COUNT(*) as visit_count'))
            ->whereBetween('visit_date', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString()
            ])
            ->groupBy('exhibit_id')
            ->orderByDesc('visit_count')
            ->limit(5)
            ->with('exhibit:id,name')
            ->get();

        // Daily visits for last 7 days
        $daily_visits = ExhibitVisit::where('visit_date', '>=', now()->subDays(7)->toDateString())
            ->select('visit_date', DB::raw('COUNT(*) as count'))
            ->groupBy('visit_date')
            ->orderBy('visit_date')
            ->get();

        $max_daily_visits = $daily_visits->max('count') ?? 1;

        $recent_exhibits = Exhibit::orderBy('created_at', 'desc')->take(10)->get();
        $recent_users = User::orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.dashboard', compact(
            'stats',
            'visit_stats',
            'most_visited',
            'daily_visits',
            'max_daily_visits',
            'recent_exhibits',
            'recent_users'
        ));
    }
}
