@extends('admin.layout')

@section('title', 'Edit Floor Plan')
@section('breadcrumb', 'Home / Floor Plans / Edit')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Floor Plan: {{ $floorPlan->name }}</h3>
    </div>

    <form method="POST" action="{{ route('admin.floor-plans.update', $floorPlan->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Exhibit</label>
            <select name="exhibit_id" class="form-control" required>
                <option value="">Select Exhibit</option>
                @foreach($exhibits as $exhibit)
                    <option value="{{ $exhibit->id }}" {{ $floorPlan->exhibit_id == $exhibit->id ? 'selected' : '' }}>{{ $exhibit->name }}</option>
                @endforeach
            </select>
            @error('exhibit_id') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $floorPlan->name) }}" required>
            @error('name') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>Floor Level</label>
            <input type="number" name="floor_level" class="form-control" value="{{ old('floor_level', $floorPlan->floor_level) }}" required>
            @error('floor_level') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Width (m)</label>
                <input type="number" step="0.01" name="width" class="form-control" value="{{ old('width', $floorPlan->width) }}">
            </div>
            <div class="form-group">
                <label>Length (m)</label>
                <input type="number" step="0.01" name="length" class="form-control" value="{{ old('length', $floorPlan->length) }}">
            </div>
            <div class="form-group">
                <label>Height (m)</label>
                <input type="number" step="0.01" name="height" class="form-control" value="{{ old('height', $floorPlan->height) }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Origin Latitude</label>
                <input type="number" step="0.00000001" name="origin_latitude" class="form-control" value="{{ old('origin_latitude', $floorPlan->origin_latitude) }}">
            </div>
            <div class="form-group">
                <label>Origin Longitude</label>
                <input type="number" step="0.00000001" name="origin_longitude" class="form-control" value="{{ old('origin_longitude', $floorPlan->origin_longitude) }}">
            </div>
            <div class="form-group">
                <label>Origin Altitude</label>
                <input type="number" step="0.01" name="origin_altitude" class="form-control" value="{{ old('origin_altitude', $floorPlan->origin_altitude) }}">
            </div>
        </div>

        <div class="form-group">
            <label>3D Model File (leave empty to keep current)</label>
            <input type="file" name="model_file" class="form-control">
            @if($floorPlan->model_file_path)
                <p style="font-size: 13px; color: #666; margin-top: 5px;">Current: {{ $floorPlan->model_file_path }}</p>
            @endif
        </div>

        <div class="form-group">
            <label>Thumbnail Image (leave empty to keep current)</label>
            <input type="file" name="thumbnail" class="form-control" accept="image/*">
            @if($floorPlan->thumbnail_path)
                <p style="font-size: 13px; color: #666; margin-top: 5px;">Current: {{ $floorPlan->thumbnail_path }}</p>
            @endif
        </div>

        <div class="form-group">
            <label>
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $floorPlan->is_active) ? 'checked' : '' }}> Active
            </label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Update Floor Plan</button>
            <a href="{{ route('admin.floor-plans.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
