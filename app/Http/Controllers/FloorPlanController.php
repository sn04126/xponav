<?php

namespace App\Http\Controllers;

use App\Models\ExhibitFloorPlan;
use App\Http\Requests\StoreFloorPlanRequest;
use App\Http\Requests\UpdateFloorPlanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FloorPlanController extends Controller
{
    /**
     * Display a listing of floor plans
     */
    public function index(Request $request)
    {
        $query = ExhibitFloorPlan::with(['exhibit', 'arAnchors']);

        // Filter by exhibit
        if ($request->has('exhibit_id')) {
            $query->byExhibit($request->exhibit_id);
        }

        // Filter by floor level
        if ($request->has('floor_level')) {
            $query->byLevel($request->floor_level);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $floorPlans = $query->latest()->paginate($request->get('per_page', 15));

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $floorPlans,
            ]);
        }

        return view('admin.floor-plans.index', compact('floorPlans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $exhibits = \App\Models\Exhibit::all();
        return view('admin.floor-plans.create', compact('exhibits'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $floorPlan = ExhibitFloorPlan::findOrFail($id);
        $exhibits = \App\Models\Exhibit::all();
        return view('admin.floor-plans.edit', compact('floorPlan', 'exhibits'));
    }

    /**
     * Store a newly created floor plan
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'exhibit_id' => 'required|exists:exhibits,id',
            'name' => 'required|string|max:255',
            'floor_level' => 'required|integer',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'origin_latitude' => 'nullable|numeric',
            'origin_longitude' => 'nullable|numeric',
            'origin_altitude' => 'nullable|numeric',
            'is_active' => 'boolean',
            'model_file' => 'nullable|file|max:10240',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('model_file')) {
            $modelPath = $request->file('model_file')->store('floor_plans/models', 'public');
            $data['model_file_path'] = $modelPath;
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('floor_plans/thumbnails', 'public');
            $data['thumbnail_path'] = $thumbnailPath;
        }

        // Remove file fields from data array
        unset($data['model_file'], $data['thumbnail']);

        $floorPlan = ExhibitFloorPlan::create($data);

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Floor plan created successfully',
                'data' => $floorPlan,
            ], 201);
        }

        return redirect()->route('admin.floor-plans.index')->with('success', 'Floor plan created successfully!');
    }

    /**
     * Display the specified floor plan
     */
    public function show($id)
    {
        $floorPlan = ExhibitFloorPlan::with(['exhibit', 'arAnchors', 'activeAnchors'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $floorPlan,
        ]);
    }

    /**
     * Update the specified floor plan
     */
    public function update(Request $request, $id)
    {
        $floorPlan = ExhibitFloorPlan::findOrFail($id);
        $data = $request->validate([
            'exhibit_id' => 'required|exists:exhibits,id',
            'name' => 'required|string|max:255',
            'floor_level' => 'required|integer',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'origin_latitude' => 'nullable|numeric',
            'origin_longitude' => 'nullable|numeric',
            'origin_altitude' => 'nullable|numeric',
            'is_active' => 'boolean',
            'model_file' => 'nullable|file|max:10240',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        // Handle 3D model file upload
        if ($request->hasFile('model_file')) {
            // Delete old model file
            if ($floorPlan->model_file_path) {
                Storage::disk('public')->delete($floorPlan->model_file_path);
            }

            $modelPath = $request->file('model_file')->store('floor_plans/models', 'public');
            $data['model_file_path'] = $modelPath;
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($floorPlan->thumbnail_path) {
                Storage::disk('public')->delete($floorPlan->thumbnail_path);
            }

            $thumbnailPath = $request->file('thumbnail')->store('floor_plans/thumbnails', 'public');
            $data['thumbnail_path'] = $thumbnailPath;
        }

        // Remove file fields from data array
        unset($data['model_file'], $data['thumbnail']);

        $floorPlan->update($data);

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            $floorPlan->load(['exhibit', 'arAnchors']);
            return response()->json([
                'success' => true,
                'message' => 'Floor plan updated successfully',
                'data' => $floorPlan,
            ]);
        }

        return redirect()->route('admin.floor-plans.index')->with('success', 'Floor plan updated successfully!');
    }

    /**
     * Remove the specified floor plan
     */
    public function destroy(Request $request, $id)
    {
        $floorPlan = ExhibitFloorPlan::findOrFail($id);

        // Delete associated files
        if ($floorPlan->model_file_path) {
            Storage::disk('public')->delete($floorPlan->model_file_path);
        }
        if ($floorPlan->thumbnail_path) {
            Storage::disk('public')->delete($floorPlan->thumbnail_path);
        }

        $floorPlan->delete();

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Floor plan deleted successfully',
            ]);
        }

        return redirect()->route('admin.floor-plans.index')->with('success', 'Floor plan deleted successfully!');
    }

    /**
     * Get floor plans for a specific exhibit (for mobile app)
     */
    public function byExhibit($exhibitId)
    {
        $floorPlans = ExhibitFloorPlan::with(['activeAnchors'])
            ->byExhibit($exhibitId)
            ->active()
            ->orderBy('floor_level')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $floorPlans,
        ]);
    }

    /**
     * Get floor plan with all AR anchors (for mobile app AR initialization)
     */
    public function getARData($id)
    {
        $floorPlan = ExhibitFloorPlan::with([
            'exhibit:id,title,location',
            'activeAnchors' => function($query) {
                $query->orderByPriority();
            }
        ])->active()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'floor_plan' => $floorPlan,
                'ar_configuration' => [
                    'world_origin' => [
                        'latitude' => $floorPlan->origin_latitude,
                        'longitude' => $floorPlan->origin_longitude,
                        'altitude' => $floorPlan->origin_altitude,
                    ],
                    'dimensions' => [
                        'width' => $floorPlan->width,
                        'height' => $floorPlan->height,
                        'length' => $floorPlan->length,
                    ],
                    'floor_level' => $floorPlan->floor_level,
                ],
            ],
        ]);
    }
}
