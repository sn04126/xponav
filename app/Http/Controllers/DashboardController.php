<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Get promoted exhibits
        $promotedExhibits = \App\Models\Exhibit::where('is_promoted', true)
            ->where('status', 'active')
            ->get();

        // Get upcoming exhibits (start_date is in the future)
        $upcomingExhibits = \App\Models\Exhibit::where('status', 'active')
            ->whereDate('start_date', '>', $today)
            ->orderBy('start_date', 'asc')
            ->get();

        return response()->json([
            'promoted_exhibits' => \App\Http\Resources\ExhibitResource::collection($promotedExhibits),
            'upcoming_exhibits' => \App\Http\Resources\ExhibitResource::collection($upcomingExhibits),
        ]);
    }
}
