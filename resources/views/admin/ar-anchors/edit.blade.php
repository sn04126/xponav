@extends('admin.layout')

@section('title', 'Edit AR Anchor')
@section('breadcrumb', 'Home / AR Anchors / Edit')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit AR Anchor: {{ $anchor->anchor_name }}</h3>
    </div>

    <form method="POST" action="{{ route('admin.ar-anchors.update', $anchor->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Floor Plan</label>
                <select name="floor_plan_id" class="form-control" required>
                    <option value="">Select Floor Plan</option>
                    @foreach($floorPlans as $fp)
                        <option value="{{ $fp->id }}" {{ $anchor->floor_plan_id == $fp->id ? 'selected' : '' }}>{{ $fp->name }} ({{ $fp->exhibit->name ?? 'N/A' }})</option>
                    @endforeach
                </select>
                @error('floor_plan_id') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label>Exhibit (optional)</label>
                <select name="exhibit_id" class="form-control">
                    <option value="">None</option>
                    @foreach($exhibits as $exhibit)
                        <option value="{{ $exhibit->id }}" {{ $anchor->exhibit_id == $exhibit->id ? 'selected' : '' }}>{{ $exhibit->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Anchor Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $anchor->anchor_name) }}" required>
                @error('name') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label>Anchor Type</label>
                <select name="anchor_type" class="form-control" required>
                    <option value="reference_point" {{ $anchor->anchor_type == 'reference_point' ? 'selected' : '' }}>Reference Point</option>
                    <option value="exhibit_location" {{ $anchor->anchor_type == 'exhibit_location' ? 'selected' : '' }}>Exhibit Location</option>
                    <option value="navigation_point" {{ $anchor->anchor_type == 'navigation_point' ? 'selected' : '' }}>Navigation Point</option>
                    <option value="entrance" {{ $anchor->anchor_type == 'entrance' ? 'selected' : '' }}>Entrance</option>
                    <option value="exit" {{ $anchor->anchor_type == 'exit' ? 'selected' : '' }}>Exit</option>
                </select>
                @error('anchor_type') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Position X</label>
                <input type="number" step="0.0001" name="position_x" class="form-control" value="{{ old('position_x', $anchor->position_x) }}" required>
            </div>
            <div class="form-group">
                <label>Position Y</label>
                <input type="number" step="0.0001" name="position_y" class="form-control" value="{{ old('position_y', $anchor->position_y) }}" required>
            </div>
            <div class="form-group">
                <label>Position Z</label>
                <input type="number" step="0.0001" name="position_z" class="form-control" value="{{ old('position_z', $anchor->position_z) }}" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Rotation X</label>
                <input type="number" step="0.000001" name="rotation_x" class="form-control" value="{{ old('rotation_x', $anchor->rotation_x) }}">
            </div>
            <div class="form-group">
                <label>Rotation Y</label>
                <input type="number" step="0.000001" name="rotation_y" class="form-control" value="{{ old('rotation_y', $anchor->rotation_y) }}">
            </div>
            <div class="form-group">
                <label>Rotation Z</label>
                <input type="number" step="0.000001" name="rotation_z" class="form-control" value="{{ old('rotation_z', $anchor->rotation_z) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Priority</label>
            <input type="number" name="priority" class="form-control" value="{{ old('priority', $anchor->priority) }}">
        </div>

        <div class="form-group">
            <label>Marker Image (leave empty to keep current)</label>
            <input type="file" name="marker_image" class="form-control" accept="image/*">
            @if($anchor->marker_image_path)
                <p style="font-size: 13px; color: #666; margin-top: 5px;">Current: {{ $anchor->marker_image_path }}</p>
            @endif
        </div>

        <div class="form-group">
            <label>
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $anchor->is_active) ? 'checked' : '' }}> Active
            </label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Update Anchor</button>
            <a href="{{ route('admin.ar-anchors.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
