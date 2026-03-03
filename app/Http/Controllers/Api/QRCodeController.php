<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LocationQRCode;
use App\Models\Exhibit;
use App\Models\ExhibitFloorPlan;
use Illuminate\Http\Request;

class QRCodeController extends Controller
{
    /**
     * Scan a QR code and get location data for AR initialization
     * This is the main endpoint called by the Unity app when scanning a QR code
     */
    public function scan(string $code)
    {
        $qrCode = LocationQRCode::with(['exhibit', 'floorPlan', 'anchor'])
            ->where('code', $code)
            ->active()
            ->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive QR code'
            ], 404);
        }

        // Increment scan count for analytics
        $qrCode->incrementScanCount();

        // Get all floor plans for this exhibit
        $floorPlans = ExhibitFloorPlan::where('exhibit_id', $qrCode->exhibit_id)
            ->orderBy('floor_level')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'QR code scanned successfully',
            'data' => [
                'qr_code' => [
                    'id' => $qrCode->id,
                    'code' => $qrCode->code,
                    'name' => $qrCode->name,
                    'description' => $qrCode->description,
                ],
                'location' => [
                    'position_x' => $qrCode->position_x,
                    'position_y' => $qrCode->position_y,
                    'position_z' => $qrCode->position_z,
                    'rotation_y' => $qrCode->rotation_y,
                ],
                'exhibit' => [
                    'id' => $qrCode->exhibit->id,
                    'name' => $qrCode->exhibit->name,
                    'description' => $qrCode->exhibit->description,
                    'image' => $qrCode->exhibit->image,
                ],
                'floor_plan' => [
                    'id' => $qrCode->floorPlan->id,
                    'name' => $qrCode->floorPlan->name,
                    'floor_level' => $qrCode->floorPlan->floor_level,
                ],
                'floor_plans' => $floorPlans->map(function ($fp) {
                    return [
                        'id' => $fp->id,
                        'name' => $fp->name,
                        'floor_level' => $fp->floor_level,
                    ];
                }),
                'anchor' => $qrCode->anchor ? [
                    'id' => $qrCode->anchor->id,
                    'name' => $qrCode->anchor->anchor_name,
                    'type' => $qrCode->anchor->anchor_type,
                ] : null,
            ]
        ]);
    }

    /**
     * Get all QR codes for an exhibit (for admin management)
     */
    public function byExhibit(Exhibit $exhibit)
    {
        $qrCodes = LocationQRCode::with(['floorPlan', 'anchor'])
            ->where('exhibit_id', $exhibit->id)
            ->orderBy('floor_plan_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $qrCodes
        ]);
    }

    /**
     * Create a new QR code location
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exhibit_id' => 'required|exists:exhibits,id',
            'floor_plan_id' => 'required|exists:exhibit_floor_plans,id',
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
            'position_z' => 'required|numeric',
            'rotation_y' => 'nullable|numeric',
            'anchor_id' => 'nullable|exists:ar_anchors,id',
        ]);

        $qrCode = LocationQRCode::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'QR code created successfully',
            'data' => $qrCode->load(['exhibit', 'floorPlan', 'anchor'])
        ], 201);
    }

    /**
     * Update a QR code
     */
    public function update(Request $request, LocationQRCode $qrCode)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'position_x' => 'sometimes|numeric',
            'position_y' => 'sometimes|numeric',
            'position_z' => 'sometimes|numeric',
            'rotation_y' => 'nullable|numeric',
            'anchor_id' => 'nullable|exists:ar_anchors,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $qrCode->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'QR code updated successfully',
            'data' => $qrCode->fresh(['exhibit', 'floorPlan', 'anchor'])
        ]);
    }

    /**
     * Delete a QR code
     */
    public function destroy(LocationQRCode $qrCode)
    {
        $qrCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'QR code deleted successfully'
        ]);
    }

    /**
     * Regenerate QR code identifier
     */
    public function regenerateCode(LocationQRCode $qrCode)
    {
        $qrCode->code = LocationQRCode::generateUniqueCode();
        $qrCode->save();

        return response()->json([
            'success' => true,
            'message' => 'QR code regenerated successfully',
            'data' => [
                'code' => $qrCode->code,
                'qr_url' => $qrCode->getQRCodeUrl()
            ]
        ]);
    }

    /**
     * Get QR code statistics
     */
    public function statistics(Exhibit $exhibit)
    {
        $qrCodes = LocationQRCode::where('exhibit_id', $exhibit->id)->get();

        $totalScans = $qrCodes->sum('scan_count');
        $activeCount = $qrCodes->where('is_active', true)->count();
        $topScanned = $qrCodes->sortByDesc('scan_count')->take(5)->values();

        return response()->json([
            'success' => true,
            'data' => [
                'total_qr_codes' => $qrCodes->count(),
                'active_qr_codes' => $activeCount,
                'total_scans' => $totalScans,
                'top_scanned' => $topScanned->map(function ($qr) {
                    return [
                        'id' => $qr->id,
                        'name' => $qr->name,
                        'code' => $qr->code,
                        'scan_count' => $qr->scan_count,
                    ];
                })
            ]
        ]);
    }
}
