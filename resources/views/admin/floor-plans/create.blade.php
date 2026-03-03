@extends('admin.layout')

@section('title', 'Create Floor Plan')
@section('breadcrumb', 'Home / Floor Plans / Create')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Add New Floor Plan</h3>
    </div>

    <form method="POST" action="{{ route('admin.floor-plans.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Exhibit</label>
            <select name="exhibit_id" class="form-control" required>
                <option value="">Select Exhibit</option>
                @foreach($exhibits as $exhibit)
                    <option value="{{ $exhibit->id }}" {{ old('exhibit_id') == $exhibit->id ? 'selected' : '' }}>{{ $exhibit->name }}</option>
                @endforeach
            </select>
            @error('exhibit_id') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Ground Floor - Main Hall" required>
            @error('name') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>Floor Level</label>
            <input type="number" name="floor_level" class="form-control" value="{{ old('floor_level', 0) }}" required>
            @error('floor_level') <p style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</p> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Width (m)</label>
                <input type="number" step="0.01" name="width" class="form-control" value="{{ old('width') }}">
            </div>
            <div class="form-group">
                <label>Length (m)</label>
                <input type="number" step="0.01" name="length" class="form-control" value="{{ old('length') }}">
            </div>
            <div class="form-group">
                <label>Height (m)</label>
                <input type="number" step="0.01" name="height" class="form-control" value="{{ old('height') }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Origin Latitude</label>
                <input type="number" step="0.00000001" name="origin_latitude" class="form-control" value="{{ old('origin_latitude') }}">
            </div>
            <div class="form-group">
                <label>Origin Longitude</label>
                <input type="number" step="0.00000001" name="origin_longitude" class="form-control" value="{{ old('origin_longitude') }}">
            </div>
            <div class="form-group">
                <label>Origin Altitude</label>
                <input type="number" step="0.01" name="origin_altitude" class="form-control" value="{{ old('origin_altitude') }}">
            </div>
        </div>

        <div class="form-group">
            <label>3D Model File</label>
            <input type="file" name="model_file" class="form-control">
        </div>

        <div class="form-group">
            <label>Thumbnail Image</label>
            <input type="file" name="thumbnail" class="form-control" accept="image/*">
        </div>

        <div class="form-group">
            <label>
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}> Active
            </label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Create Floor Plan</button>
            <a href="{{ route('admin.floor-plans.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
