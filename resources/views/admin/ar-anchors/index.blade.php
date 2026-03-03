@extends('admin.layout')

@section('title', 'AR Anchors Management')
@section('breadcrumb', 'Home / AR Anchors')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>All AR Anchors</h2>
    <a href="{{ route('admin.ar-anchors.create') }}" class="btn btn-primary">+ Add New Anchor</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Floor Plan</th>
                <th>Exhibit</th>
                <th>Position (X, Y, Z)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($anchors as $anchor)
            <tr>
                <td>#{{ $anchor->id }}</td>
                <td><strong>{{ $anchor->anchor_name }}</strong></td>
                <td>
                    <span class="badge badge-{{ $anchor->anchor_type === 'entrance' ? 'success' : ($anchor->anchor_type === 'exhibit_location' ? 'warning' : 'info') }}" style="{{ $anchor->anchor_type === 'navigation_point' ? 'background: #d1ecf1; color: #0c5460;' : '' }}">
                        {{ str_replace('_', ' ', ucfirst($anchor->anchor_type)) }}
                    </span>
                </td>
                <td>{{ $anchor->floorPlan->name ?? 'N/A' }}</td>
                <td>{{ $anchor->exhibit->name ?? 'N/A' }}</td>
                <td>{{ number_format($anchor->position_x, 1) }}, {{ number_format($anchor->position_y, 1) }}, {{ number_format($anchor->position_z, 1) }}</td>
                <td>
                    @if($anchor->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.ar-anchors.edit', $anchor->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <form action="{{ route('admin.ar-anchors.destroy', $anchor->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this anchor?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    No AR anchors found. <a href="{{ route('admin.ar-anchors.create') }}">Create your first anchor</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($anchors->hasPages())
<div style="margin-top: 20px;">
    {{ $anchors->links() }}
</div>
@endif
@endsection
