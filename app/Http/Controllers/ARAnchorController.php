<?php

namespace App\Http\Controllers;

use App\Models\ARAnchor;
use App\Models\ExhibitFloorPlan;
use App\Http\Requests\StoreARAnchorRequest;
use App\Http\Requests\UpdateARAnchorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ARAnchorController extends Controller
{
    /**
     * Display a listing of AR anchors
     */
    public function index(Request $request)
    {
        $query = ARAnchor::with(['floorPlan', 'exhibit']);

        // Filter by floor plan
        if ($request->has('floor_plan_id')) {
            $query->byFloorPlan($request->floor_plan_id);
        }

        // Filter by anchor type
        if ($request->has('anchor_type')) {
            $query->byType($request->anchor_type);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by exhibit
        if ($request->has('exhibit_id')) {
            $query->where('exhibit_id', $request->exhibit_id);
        }

        $anchors = $query->orderByPriority()->paginate($request->get('per_page', 15));

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $anchors,
            ]);
        }

        return view('admin.ar-anchors.index', compact('anchors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $floorPlans = \App\Models\ExhibitFloorPlan::with('exhibit')->get();
        $exhibits = \App\Models\Exhibit::all();
        return view('admin.ar-anchors.create', compact('floorPlans', 'exhibits'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $anchor = ARAnchor::findOrFail($id);
        $floorPlans = \App\Models\ExhibitFloorPlan::with('exhibit')->get();
        $exhibits = \App\Models\Exhibit::all();
        return view('admin.ar-anchors.edit', compact('anchor', 'floorPlans', 'exhibits'));
    }

    /**
     * Store a newly created AR anchor
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'floor_plan_id' => 'required|exists:exhibit_floor_plans,id',
            'exhibit_id' => 'nullable|exists:exhibits,id',
            'name' => 'required|string|max:255',
            'anchor_type' => 'required|string|max:50',
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
            'position_z' => 'required|numeric',
            'rotation_x' => 'nullable|numeric',
            'rotation_y' => 'nullable|numeric',
            'rotation_z' => 'nullable|numeric',
            'priority' => 'nullable|integer',
            'is_active' => 'boolean',
            'marker_image' => 'nullable|image|max:2048',
        ]);

        // Map form field 'name' to database column 'anchor_name'
        $data['anchor_name'] = $data['name'];
        unset($data['name']);

        // Handle is_active checkbox (unchecked = not sent)
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle marker image upload
        if ($request->hasFile('marker_image')) {
            $markerPath = $request->file('marker_image')->store('ar_anchors/markers', 'public');
            $data['marker_image_path'] = $markerPath;
        }

        // Remove file field from data array
        unset($data['marker_image']);

        $anchor = ARAnchor::create($data);

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            $anchor->load(['floorPlan', 'exhibit']);
            return response()->json([
                'success' => true,
                'message' => 'AR anchor created successfully',
                'data' => $anchor,
            ], 201);
        }

        return redirect()->route('admin.ar-anchors.index')->with('success', 'AR anchor created successfully!');
    }

    /**
     * Display the specified AR anchor
     */
    public function show($id)
    {
        $anchor = ARAnchor::with(['floorPlan.exhibit', 'exhibit'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $anchor,
        ]);
    }

    /**
     * Update the specified AR anchor
     */
    public function update(Request $request, $id)
    {
        $anchor = ARAnchor::findOrFail($id);
        $data = $request->validate([
            'floor_plan_id' => 'required|exists:exhibit_floor_plans,id',
            'exhibit_id' => 'nullable|exists:exhibits,id',
            'name' => 'required|string|max:255',
            'anchor_type' => 'required|string|max:50',
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
            'position_z' => 'required|numeric',
            'rotation_x' => 'nullable|numeric',
            'rotation_y' => 'nullable|numeric',
            'rotation_z' => 'nullable|numeric',
            'priority' => 'nullable|integer',
            'is_active' => 'boolean',
            'marker_image' => 'nullable|image|max:2048',
        ]);

        // Map form field 'name' to database column 'anchor_name'
        $data['anchor_name'] = $data['name'];
        unset($data['name']);

        // Handle is_active checkbox (unchecked = not sent)
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle marker image upload
        if ($request->hasFile('marker_image')) {
            // Delete old marker image
            if ($anchor->marker_image_path) {
                Storage::disk('public')->delete($anchor->marker_image_path);
            }

            $markerPath = $request->file('marker_image')->store('ar_anchors/markers', 'public');
            $data['marker_image_path'] = $markerPath;
        }

        // Remove file field from data array
        unset($data['marker_image']);

        $anchor->update($data);

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            $anchor->load(['floorPlan', 'exhibit']);
            return response()->json([
                'success' => true,
                'message' => 'AR anchor updated successfully',
                'data' => $anchor,
            ]);
        }

        return redirect()->route('admin.ar-anchors.index')->with('success', 'AR anchor updated successfully!');
    }

    /**
     * Remove the specified AR anchor
     */
    public function destroy(Request $request, $id)
    {
        $anchor = ARAnchor::findOrFail($id);

        // Delete associated marker image
        if ($anchor->marker_image_path) {
            Storage::disk('public')->delete($anchor->marker_image_path);
        }

        $anchor->delete();

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'AR anchor deleted successfully',
            ]);
        }

        return redirect()->route('admin.ar-anchors.index')->with('success', 'AR anchor deleted successfully!');
    }

    /**
     * Get AR anchors for a specific floor plan (for mobile app)
     */
    public function byFloorPlan($floorPlanId)
    {
        $anchors = ARAnchor::with(['exhibit'])
            ->byFloorPlan($floorPlanId)
            ->active()
            ->orderByPriority()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $anchors,
        ]);
    }

    /**
     * Get reference points for AR initialization (for mobile app)
     */
    public function getReferencePoints($floorPlanId)
    {
        $referencePoints = ARAnchor::byFloorPlan($floorPlanId)
            ->byType('reference_point')
            ->active()
            ->orderByPriority()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $referencePoints,
        ]);
    }

    /**
     * Calculate user position based on detected anchors (for mobile app)
     * This endpoint receives detected anchor data from ARKit and helps triangulate position
     */
    public function calculatePosition(Request $request)
    {
        $request->validate([
            'floor_plan_id' => 'required|exists:exhibit_floor_plans,id',
            'detected_anchors' => 'required|array|min:1',
            'detected_anchors.*.anchor_id' => 'required|exists:ar_anchors,id',
            'detected_anchors.*.distance' => 'required|numeric|min:0',
            'detected_anchors.*.position' => 'required|array',
            'detected_anchors.*.position.x' => 'required|numeric',
            'detected_anchors.*.position.y' => 'required|numeric',
            'detected_anchors.*.position.z' => 'required|numeric',
        ]);

        $floorPlan = ExhibitFloorPlan::findOrFail($request->floor_plan_id);
        $detectedAnchors = $request->detected_anchors;

        // Get anchor details from database
        $anchorIds = array_column($detectedAnchors, 'anchor_id');
        $dbAnchors = ARAnchor::whereIn('id', $anchorIds)->get()->keyBy('id');

        // Calculate weighted average position based on detected anchors
        $totalWeight = 0;
        $weightedX = 0;
        $weightedY = 0;
        $weightedZ = 0;

        foreach ($detectedAnchors as $detected) {
            $anchor = $dbAnchors->get($detected['anchor_id']);
            if (!$anchor) continue;

            // Weight is inversely proportional to distance (closer anchors have more weight)
            $weight = 1 / (1 + $detected['distance']);
            $totalWeight += $weight;

            // Use device-reported position relative to anchor
            $weightedX += ($anchor->position_x + $detected['position']['x']) * $weight;
            $weightedY += ($anchor->position_y + $detected['position']['y']) * $weight;
            $weightedZ += ($anchor->position_z + $detected['position']['z']) * $weight;
        }

        if ($totalWeight > 0) {
            $estimatedPosition = [
                'x' => $weightedX / $totalWeight,
                'y' => $weightedY / $totalWeight,
                'z' => $weightedZ / $totalWeight,
            ];
        } else {
            $estimatedPosition = null;
        }

        // Find nearby exhibits based on estimated position
        $nearbyExhibits = [];
        if ($estimatedPosition) {
            $exhibitAnchors = ARAnchor::byFloorPlan($request->floor_plan_id)
                ->byType('exhibit_location')
                ->active()
                ->with('exhibit')
                ->get();

            foreach ($exhibitAnchors as $exhibitAnchor) {
                $distance = sqrt(
                    pow($exhibitAnchor->position_x - $estimatedPosition['x'], 2) +
                    pow($exhibitAnchor->position_y - $estimatedPosition['y'], 2) +
                    pow($exhibitAnchor->position_z - $estimatedPosition['z'], 2)
                );

                if ($distance <= 10) { // Within 10 meters
                    $nearbyExhibits[] = [
                        'exhibit' => $exhibitAnchor->exhibit,
                        'distance' => round($distance, 2),
                        'anchor' => $exhibitAnchor,
                    ];
                }
            }

            // Sort by distance
            usort($nearbyExhibits, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
        }

        return response()->json([
            'success' => true,
            'data' => [
                'estimated_position' => $estimatedPosition,
                'floor_plan' => [
                    'id' => $floorPlan->id,
                    'name' => $floorPlan->name,
                    'floor_level' => $floorPlan->floor_level,
                ],
                'nearby_exhibits' => $nearbyExhibits,
                'detected_anchor_count' => count($detectedAnchors),
            ],
        ]);
    }

    /**
     * Update AR world map data for an anchor (for mobile app)
     * This allows the app to save ARWorldMap data for better relocalization
     */
    public function updateWorldMap(Request $request, $id)
    {
        $request->validate([
            'ar_world_map_data' => 'required|array',
            'ar_anchor_identifier' => 'nullable|string',
        ]);

        $anchor = ARAnchor::findOrFail($id);
        
        $anchor->update([
            'ar_world_map_data' => $request->ar_world_map_data,
            'ar_anchor_identifier' => $request->ar_anchor_identifier ?? $anchor->ar_anchor_identifier,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'AR world map data updated successfully',
            'data' => $anchor,
        ]);
    }

    /**
     * Get navigation path between two points (for mobile app)
     */
    public function getNavigationPath(Request $request)
    {
        $request->validate([
            'floor_plan_id' => 'required|exists:exhibit_floor_plans,id',
            'from_anchor_id' => 'required|exists:ar_anchors,id',
            'to_anchor_id' => 'required|exists:ar_anchors,id',
        ]);

        $fromAnchor = ARAnchor::findOrFail($request->from_anchor_id);
        $toAnchor = ARAnchor::findOrFail($request->to_anchor_id);

        // Get navigation points between the two anchors
        $navigationPoints = ARAnchor::byFloorPlan($request->floor_plan_id)
            ->byType('navigation_point')
            ->active()
            ->orderByPriority()
            ->get();

        // Simple path: direct line with navigation waypoints
        $path = [
            [
                'position' => $fromAnchor->position,
                'type' => 'start',
                'anchor' => $fromAnchor,
            ],
        ];

        // Add relevant navigation points (simplified - in production use A* or similar)
        foreach ($navigationPoints as $navPoint) {
            $path[] = [
                'position' => $navPoint->position,
                'type' => 'waypoint',
                'anchor' => $navPoint,
            ];
        }

        $path[] = [
            'position' => $toAnchor->position,
            'type' => 'destination',
            'anchor' => $toAnchor,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'path' => $path,
                'total_waypoints' => count($path),
            ],
        ]);
    }
}

