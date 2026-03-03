@extends('admin.layout')

@section('title', 'Create AR Anchor')
@section('breadcrumb', 'Home / AR Anchors / Create')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Add New AR Anchor</h3>
    </div>

    <form method="POST" action="{{ route('admin.ar-anchors.store') }}" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Floor Plan</label>
                <select name="floor_plan_id" class="form-control" required>
                    <option value="">Select Floor Plan</option>
                    @foreach($floorPlans as $fp)
                        <option value="{{ $fp->id }}" {{ old('floor_plan_id') == $fp->id ? 'selected' : '' }}>{{ $fp->name }} ({{ $fp->exhibit->name ?? 'N/A' }})</option>
                    @endforeach
                </select>
                @error('floor_plan_id') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label>Exhibit (optional)</label>
                <select name="exhibit_id" class="form-control">
                    <option value="">None</option>
                    @foreach($exhibits as $exhibit)
                        <option value="{{ $exhibit->id }}" {{ old('exhibit_id') == $exhibit->id ? 'selected' : '' }}>{{ $exhibit->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Anchor Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Main Entrance" required>
                @error('name') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label>Anchor Type</label>
                <select name="anchor_type" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="reference_point" {{ old('anchor_type') == 'reference_point' ? 'selected' : '' }}>Reference Point</option>
                    <option value="exhibit_location" {{ old('anchor_type') == 'exhibit_location' ? 'selected' : '' }}>Exhibit Location</option>
                    <option value="navigation_point" {{ old('anchor_type') == 'navigation_point' ? 'selected' : '' }}>Navigation Point</option>
                    <option value="entrance" {{ old('anchor_type') == 'entrance' ? 'selected' : '' }}>Entrance</option>
                    <option value="exit" {{ old('anchor_type') == 'exit' ? 'selected' : '' }}>Exit</option>
                </select>
                @error('anchor_type') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Position X</label>
                <input type="number" step="0.0001" name="position_x" class="form-control" value="{{ old('position_x', 0) }}" required>
            </div>
            <div class="form-group">
                <label>Position Y</label>
                <input type="number" step="0.0001" name="position_y" class="form-control" value="{{ old('position_y', 0) }}" required>
            </div>
            <div class="form-group">
                <label>Position Z</label>
                <input type="number" step="0.0001" name="position_z" class="form-control" value="{{ old('position_z', 0) }}" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Rotation X</label>
                <input type="number" step="0.000001" name="rotation_x" class="form-control" value="{{ old('rotation_x', 0) }}">
            </div>
            <div class="form-group">
                <label>Rotation Y</label>
                <input type="number" step="0.000001" name="rotation_y" class="form-control" value="{{ old('rotation_y', 0) }}">
            </div>
            <div class="form-group">
                <label>Rotation Z</label>
                <input type="number" step="0.000001" name="rotation_z" class="form-control" value="{{ old('rotation_z', 0) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Priority</label>
            <input type="number" name="priority" class="form-control" value="{{ old('priority', 0) }}">
        </div>

        <div class="form-group">
            <label>Marker Image (optional)</label>
            <input type="file" name="marker_image" class="form-control" accept="image/*">
        </div>

        <div class="form-group">
            <label>
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}> Active
            </label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Create Anchor</button>
            <a href="{{ route('admin.ar-anchors.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
