<?php

namespace App\Http\Controllers;

use App\Models\InteractiveSession;
use App\Http\Requests\StoreInteractiveSessionRequest;
use App\Http\Requests\UpdateInteractiveSessionRequest;
use App\Http\Resources\InteractiveSessionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class InteractiveSessionController extends Controller
{
    /**
     * Copy an uploaded image to public/storage/ so it's accessible
     * even when the storage:link symlink isn't set up.
     */
    private function syncToPublic(string $path): void
    {
        $source = storage_path('app/public/' . $path);
        $dest = public_path('storage/' . $path);

        File::ensureDirectoryExists(dirname($dest));
        if (file_exists($source)) {
            copy($source, $dest);
        }
    }

    /**
     * Delete an image from both storage and public/storage/.
     */
    private function deleteImage(string $path): void
    {
        Storage::disk('public')->delete($path);
        $publicFile = public_path('storage/' . $path);
        if (file_exists($publicFile)) {
            unlink($publicFile);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            $now = now();
            $upcoming = InteractiveSession::where('date', '>=', $now->toDateString())
                ->orderBy('date')->orderBy('time')->get();
            $past = InteractiveSession::where('date', '<', $now->toDateString())
                ->orderBy('date', 'desc')->orderBy('time', 'desc')->get();

            // Get booked sessions for the authenticated user
            $booked = collect();
            if ($request->user()) {
                $booked = $request->user()->interactiveSessions()
                    ->orderBy('date', 'desc')->get();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'upcoming' => InteractiveSessionResource::collection($upcoming)->resolve(),
                    'past' => InteractiveSessionResource::collection($past)->resolve(),
                    'booked' => InteractiveSessionResource::collection($booked)->resolve(),
                ],
            ]);
        }

        $sessions = InteractiveSession::orderBy('date', 'desc')->orderBy('time', 'desc')->paginate(15);
        return view('admin.sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sessions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'hosted_by' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sessions', 'public');
            $validated['image'] = $path;
            $this->syncToPublic($path);
        }

        $session = InteractiveSession::create($validated);

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return new InteractiveSessionResource($session);
        }

        return redirect()->route('admin.sessions.index')->with('success', 'Session created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InteractiveSession $session)
    {
        return view('admin.sessions.edit', compact('session'));
    }

    /**
     * Display the specified resource.
     */
    public function show(InteractiveSession $session)
    {
        return new InteractiveSessionResource($session);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InteractiveSession $session)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'hosted_by' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($session->image) {
                $this->deleteImage($session->image);
            }
            $path = $request->file('image')->store('sessions', 'public');
            $validated['image'] = $path;
            $this->syncToPublic($path);
        }

        $session->update($validated);

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return new InteractiveSessionResource($session);
        }

        return redirect()->route('admin.sessions.index')->with('success', 'Session updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, InteractiveSession $session)
    {
        // Delete associated image
        if ($session->image) {
            $this->deleteImage($session->image);
        }

        $session->delete();

        // Check if this is an API request or web request
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Session deleted successfully'], 200);
        }

        return redirect()->route('admin.sessions.index')->with('success', 'Session deleted successfully!');
    }

    /**
     * Book/register for a session (toggle)
     */
    public function book(Request $request, InteractiveSession $interactiveSession)
    {
        $user = $request->user();

        $existingRelation = $user->interactiveSessions()
            ->where('interactive_session_id', $interactiveSession->id)
            ->first();

        if ($existingRelation) {
            // Already registered — unbook
            $user->interactiveSessions()->detach($interactiveSession->id);
            return response()->json([
                'success' => true,
                'message' => 'Session booking cancelled',
                'is_booked' => false
            ]);
        } else {
            // Book the session
            $user->interactiveSessions()->attach($interactiveSession->id, ['is_favorite' => false]);
            return response()->json([
                'success' => true,
                'message' => 'Session booked successfully',
                'is_booked' => true
            ]);
        }
    }

    /**
     * Toggle favorite status for a session
     */
    public function toggleFavorite(Request $request, InteractiveSession $interactiveSession)
    {
        $user = $request->user();
        
        $existingRelation = $user->interactiveSessions()
            ->where('interactive_session_id', $interactiveSession->id)
            ->first();
        
        if ($existingRelation) {
            $currentStatus = $existingRelation->pivot->is_favorite;
            $user->interactiveSessions()->updateExistingPivot($interactiveSession->id, [
                'is_favorite' => !$currentStatus
            ]);
            $isFavorite = !$currentStatus;
        } else {
            $user->interactiveSessions()->attach($interactiveSession->id, ['is_favorite' => true]);
            $isFavorite = true;
        }
        
        return response()->json([
            'message' => $isFavorite ? 'Added to favorites' : 'Removed from favorites',
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Get user's favorite sessions
     */
    public function favorites(Request $request)
    {
        $user = $request->user();
        $favorites = $user->favoriteInteractiveSessions;
        
        return InteractiveSessionResource::collection($favorites);
    }
}
