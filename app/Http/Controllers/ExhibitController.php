<?php

namespace App\Http\Controllers;

use App\Models\Exhibit;
use App\Http\Requests\StoreExhibitRequest;
use App\Http\Requests\UpdateExhibitRequest;
use App\Http\Resources\ExhibitResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExhibitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exhibit::query()->withCount('floorPlans');

        if ($request->has('with_floor_plans')) {
            $query->with('floorPlans');
        }

        if ($request->has('with_anchors')) {
            $query->with('arAnchors');
        }

        $exhibits = $query->paginate($request->get('per_page', 15));

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => ExhibitResource::collection($exhibits)->resolve(),
                ],
            ]);
        }

        return view('admin.exhibits.index', compact('exhibits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.exhibits.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'artist_name' => 'nullable|string|max:255',
            'artist_bio' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'ticket_price' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'boolean',
            'is_promoted' => 'boolean',
            'status' => 'nullable|string|max:50',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('exhibits', 'public');
            $validated['image'] = $path;
            $this->syncToPublic($path);
        }

        $exhibit = Exhibit::create($validated);

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return new ExhibitResource($exhibit);
        }

        return redirect()->route('admin.exhibits.index')->with('success', 'Exhibit created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exhibit $exhibit)
    {
        // Increment view count
        $exhibit->increment('view_count');
        
        $exhibit->load(['floorPlans', 'arAnchors']);
        
        return new ExhibitResource($exhibit);
    }

    /**
     * Toggle favorite status for an exhibit
     */
    public function toggleFavorite(Request $request, Exhibit $exhibit)
    {
        $user = $request->user();
        
        $existingRelation = $user->exhibits()->where('exhibit_id', $exhibit->id)->first();
        
        if ($existingRelation) {
            $currentStatus = $existingRelation->pivot->is_favorite;
            $user->exhibits()->updateExistingPivot($exhibit->id, [
                'is_favorite' => !$currentStatus
            ]);
            $isFavorite = !$currentStatus;
        } else {
            $user->exhibits()->attach($exhibit->id, ['is_favorite' => true]);
            $isFavorite = true;
        }
        
        return response()->json([
            'message' => $isFavorite ? 'Added to favorites' : 'Removed from favorites',
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Mark exhibit as visited
     */
    public function markAsVisited(Request $request, Exhibit $exhibit)
    {
        $user = $request->user();
        
        $user->exhibits()->syncWithoutDetaching([
            $exhibit->id => [
                'is_visited' => true,
                'visited_at' => now()
            ]
        ]);
        
        return response()->json([
            'message' => 'Exhibit marked as visited',
            'visited_at' => now()
        ]);
    }

    /**
     * Get user's favorite exhibits
     */
    public function favorites(Request $request)
    {
        $user = $request->user();
        $favorites = $user->favoriteExhibits;
        
        return ExhibitResource::collection($favorites);
    }

    /**
     * Get user's visited exhibits
     */
    public function visited(Request $request)
    {
        $user = $request->user();
        $visited = $user->visitedExhibits;
        
        return ExhibitResource::collection($visited);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exhibit $exhibit)
    {
        return view('admin.exhibits.edit', compact('exhibit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exhibit $exhibit)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'artist_name' => 'nullable|string|max:255',
            'artist_bio' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'ticket_price' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'boolean',
            'is_promoted' => 'boolean',
            'status' => 'nullable|string|max:50',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($exhibit->image) {
                Storage::disk('public')->delete($exhibit->image);
                $this->deleteFromPublic($exhibit->image);
            }
            $path = $request->file('image')->store('exhibits', 'public');
            $validated['image'] = $path;
            $this->syncToPublic($path);
        }

        // Handle checkboxes (unchecked = not sent in form)
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['is_promoted'] = $request->has('is_promoted') ? true : false;

        $exhibit->update($validated);

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return new ExhibitResource($exhibit);
        }

        return redirect()->route('admin.exhibits.index')->with('success', 'Exhibit updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Exhibit $exhibit)
    {
        // Delete associated image
        if ($exhibit->image) {
            Storage::disk('public')->delete($exhibit->image);
        }

        $exhibit->delete();

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Exhibit deleted successfully'], 200);
        }

        return redirect()->route('admin.exhibits.index')->with('success', 'Exhibit deleted successfully!');
    }

    private function syncToPublic(string $path): void
    {
        $src = storage_path('app/public/' . $path);
        $dest = public_path('storage/' . $path);
        $dir = dirname($dest);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (file_exists($src)) {
            copy($src, $dest);
        }
    }

    private function deleteFromPublic(string $path): void
    {
        $dest = public_path('storage/' . $path);
        if (file_exists($dest)) {
            unlink($dest);
        }
    }
}
