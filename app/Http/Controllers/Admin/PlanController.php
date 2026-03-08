<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('total_fee')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'total_fee' => 'required|numeric|min:0',
            'daily_fee' => 'required|numeric|min:0',
            'features' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Convert features textarea to JSON array
        if (!empty($validated['features'])) {
            $validated['features'] = array_map('trim', explode("\n", $validated['features']));
        } else {
            $validated['features'] = [];
        }

        Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully!');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'total_fee' => 'required|numeric|min:0',
            'daily_fee' => 'required|numeric|min:0',
            'features' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if (!empty($validated['features'])) {
            $validated['features'] = array_map('trim', explode("\n", $validated['features']));
        } else {
            $validated['features'] = [];
        }

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully!');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully!');
    }
}
