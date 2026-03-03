@extends('admin.layout')

@section('title', 'Floor Plans Management')
@section('breadcrumb', 'Home / Floor Plans')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>All Floor Plans</h2>
    <a href="{{ route('admin.floor-plans.create') }}" class="btn btn-primary">+ Add New Floor Plan</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Exhibit</th>
                <th>Floor Level</th>
                <th>Dimensions (W x L x H)</th>
                <th>AR Anchors</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($floorPlans as $floorPlan)
            <tr>
                <td>#{{ $floorPlan->id }}</td>
                <td><strong>{{ $floorPlan->name }}</strong></td>
                <td>{{ $floorPlan->exhibit->name ?? 'N/A' }}</td>
                <td>{{ $floorPlan->floor_level }}</td>
                <td>{{ $floorPlan->width ?? '-' }} x {{ $floorPlan->length ?? '-' }} x {{ $floorPlan->height ?? '-' }}m</td>
                <td>{{ $floorPlan->arAnchors->count() }}</td>
                <td>
                    @if($floorPlan->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.floor-plans.edit', $floorPlan->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <a href="{{ route('admin.ar-anchors.index', ['floor_plan_id' => $floorPlan->id]) }}" class="btn btn-sm btn-secondary">Anchors</a>
                    <form action="{{ route('admin.floor-plans.destroy', $floorPlan->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this floor plan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    No floor plans found. <a href="{{ route('admin.floor-plans.create') }}">Create your first floor plan</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($floorPlans->hasPages())
<div style="margin-top: 20px;">
    {{ $floorPlans->links() }}
</div>
@endif
@endsection
