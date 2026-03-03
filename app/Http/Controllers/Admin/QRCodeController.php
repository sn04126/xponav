<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationQRCode;
use App\Models\Exhibit;
use App\Models\ExhibitFloorPlan;
use App\Models\ARAnchor;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    /**
     * Display a listing of QR codes
     */
    public function index(Request $request)
    {
        $exhibitId = $request->get('exhibit_id');

        $query = LocationQRCode::with(['exhibit', 'floorPlan', 'anchor'])
            ->orderBy('exhibit_id')
            ->orderBy('floor_plan_id')
            ->orderBy('name');

        if ($exhibitId) {
            $query->where('exhibit_id', $exhibitId);
        }

        $qrCodes = $query->paginate(20);
        $exhibits = Exhibit::orderBy('name')->get();

        return view('admin.qr-codes.index', compact('qrCodes', 'exhibits', 'exhibitId'));
    }

    /**
     * Show the form for creating a new QR code
     */
    public function create()
    {
        $exhibits = Exhibit::orderBy('name')->get();
        $floorPlans = ExhibitFloorPlan::orderBy('exhibit_id')->orderBy('floor_level')->get();
        $anchors = ARAnchor::orderBy('floor_plan_id')->orderBy('anchor_name')->get();

        return view('admin.qr-codes.create', compact('exhibits', 'floorPlans', 'anchors'));
    }

    /**
     * Store a newly created QR code
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

        return redirect()
            ->route('admin.qr-codes.index')
            ->with('success', 'QR Code created successfully! Code: ' . $qrCode->code);
    }

    /**
     * Show the form for editing a QR code
     */
    public function edit(LocationQRCode $qrCode)
    {
        $exhibits = Exhibit::orderBy('name')->get();
        $floorPlans = ExhibitFloorPlan::where('exhibit_id', $qrCode->exhibit_id)
            ->orderBy('floor_level')
            ->get();
        $anchors = ARAnchor::where('floor_plan_id', $qrCode->floor_plan_id)
            ->orderBy('anchor_name')
            ->get();

        return view('admin.qr-codes.edit', compact('qrCode', 'exhibits', 'floorPlans', 'anchors'));
    }

    /**
     * Update the specified QR code
     */
    public function update(Request $request, LocationQRCode $qrCode)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
            'position_z' => 'required|numeric',
            'rotation_y' => 'nullable|numeric',
            'anchor_id' => 'nullable|exists:ar_anchors,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $qrCode->update($validated);

        return redirect()
            ->route('admin.qr-codes.index')
            ->with('success', 'QR Code updated successfully!');
    }

    /**
     * Remove the specified QR code
     */
    public function destroy(LocationQRCode $qrCode)
    {
        $qrCode->delete();

        return redirect()
            ->route('admin.qr-codes.index')
            ->with('success', 'QR Code deleted successfully!');
    }

    /**
     * Regenerate a QR code's identifier
     */
    public function regenerate(LocationQRCode $qrCode)
    {
        $qrCode->code = LocationQRCode::generateUniqueCode();
        $qrCode->save();

        return redirect()
            ->back()
            ->with('success', 'QR Code regenerated! New code: ' . $qrCode->code);
    }

    /**
     * Show printable QR code
     */
    public function print(LocationQRCode $qrCode)
    {
        $qrCode->load(['exhibit', 'floorPlan']);

        // Generate QR code data URL
        $scanUrl = config('app.url') . '/api/qr/scan/' . $qrCode->code;

        return view('admin.qr-codes.print', compact('qrCode', 'scanUrl'));
    }

    /**
     * Print multiple QR codes for an exhibit
     */
    public function printAll(Exhibit $exhibit)
    {
        $qrCodes = LocationQRCode::with(['floorPlan'])
            ->where('exhibit_id', $exhibit->id)
            ->where('is_active', true)
            ->orderBy('floor_plan_id')
            ->orderBy('name')
            ->get();

        $baseUrl = config('app.url') . '/api/qr/scan/';

        return view('admin.qr-codes.print-all', compact('exhibit', 'qrCodes', 'baseUrl'));
    }

    /**
     * Get floor plans for an exhibit (AJAX)
     */
    public function getFloorPlans(Exhibit $exhibit)
    {
        $floorPlans = ExhibitFloorPlan::where('exhibit_id', $exhibit->id)
            ->orderBy('floor_level')
            ->get(['id', 'name', 'floor_level']);

        return response()->json($floorPlans);
    }

    /**
     * Get anchors for a floor plan (AJAX)
     */
    public function getAnchors(ExhibitFloorPlan $floorPlan)
    {
        $anchors = ARAnchor::where('floor_plan_id', $floorPlan->id)
            ->orderBy('anchor_name')
            ->get(['id', 'anchor_name', 'anchor_type', 'position_x', 'position_y', 'position_z']);

        return response()->json($anchors);
    }
}
