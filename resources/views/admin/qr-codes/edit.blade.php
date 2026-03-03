@extends('admin.layout')

@section('title', 'Edit QR Code')
@section('breadcrumb', 'QR Codes / Edit')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Edit QR Code: {{ $qrCode->name }}</h3>
        <div>
            <code style="background: #f5f5f5; padding: 8px 16px; border-radius: 4px; font-size: 14px;">
                {{ $qrCode->code }}
            </code>
        </div>
    </div>

    <form action="{{ route('admin.qr-codes.update', $qrCode) }}" method="POST" style="padding: 20px;">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Location Name *</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $qrCode->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="2">{{ old('description', $qrCode->description) }}</textarea>
        </div>

        <div class="form-row" style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label>Exhibit</label>
                <input type="text" class="form-control" value="{{ $qrCode->exhibit->name }}" disabled>
                <small class="text-muted">Cannot be changed. Create a new QR code if needed.</small>
            </div>

            <div class="form-group" style="flex: 1;">
                <label>Floor Plan</label>
                <input type="text" class="form-control" value="Level {{ $qrCode->floorPlan->floor_level }} - {{ $qrCode->floorPlan->name }}" disabled>
            </div>
        </div>

        <h4 style="margin-top: 20px; margin-bottom: 15px;">Position</h4>

        <div class="form-row" style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label for="position_x">Position X *</label>
                <input type="number" step="0.01" name="position_x" id="position_x" class="form-control"
                       value="{{ old('position_x', $qrCode->position_x) }}" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="position_y">Position Y *</label>
                <input type="number" step="0.01" name="position_y" id="position_y" class="form-control"
                       value="{{ old('position_y', $qrCode->position_y) }}" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="position_z">Position Z *</label>
                <input type="number" step="0.01" name="position_z" id="position_z" class="form-control"
                       value="{{ old('position_z', $qrCode->position_z) }}" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="rotation_y">Rotation Y</label>
                <input type="number" step="1" name="rotation_y" id="rotation_y" class="form-control"
                       value="{{ old('rotation_y', $qrCode->rotation_y) }}">
            </div>
        </div>

        <div class="form-group">
            <label for="anchor_id">Associated Anchor</label>
            <select name="anchor_id" id="anchor_id" class="form-control">
                <option value="">None</option>
                @foreach($anchors as $anchor)
                    <option value="{{ $anchor->id }}" {{ $qrCode->anchor_id == $anchor->id ? 'selected' : '' }}>
                        {{ $anchor->anchor_name }} ({{ $anchor->anchor_type }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="checkbox-label" style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="is_active" value="1" {{ $qrCode->is_active ? 'checked' : '' }}>
                <span>Active (QR code can be scanned)</span>
            </label>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h5 style="margin-bottom: 10px;">Statistics</h5>
            <p style="margin: 0;">
                <strong>Total Scans:</strong> {{ $qrCode->scan_count }}
                <br>
                <strong>Created:</strong> {{ $qrCode->created_at->format('M d, Y H:i') }}
                <br>
                <strong>Last Updated:</strong> {{ $qrCode->updated_at->format('M d, Y H:i') }}
            </p>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Update QR Code</button>
            <a href="{{ route('admin.qr-codes.print', $qrCode) }}" class="btn btn-info" target="_blank">Print QR Code</a>
            <a href="{{ route('admin.qr-codes.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
