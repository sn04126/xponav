<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NavigationTarget;

class NavigationTargetController extends Controller
{
    /**
     * Get navigation targets for an exhibit (used by Unity app)
     */
    public function byExhibit($exhibitId)
    {
        $targets = NavigationTarget::where('exhibit_id', $exhibitId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $targets->map(function ($target) {
                return [
                    'id' => $target->id,
                    'name' => $target->name,
                    'position_x' => $target->position_x,
                    'position_y' => $target->position_y,
                    'position_z' => $target->position_z,
                    'rotation_y' => $target->rotation_y,
                    'category' => $target->category,
                    'is_active' => $target->is_active,
                ];
            }),
        ]);
    }
}
