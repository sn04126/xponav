@extends('admin.layout')

@section('title', 'Create QR Code')
@section('breadcrumb', 'QR Codes / Create')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Create Location QR Code</h3>
    </div>

    <form action="{{ route('admin.qr-codes.store') }}" method="POST" style="padding: 20px;">
        @csrf

        <div class="form-group">
            <label for="name">Location Name *</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="e.g., Main Entrance, Hall A Entry Point" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="2"
                      placeholder="Optional description for this location">{{ old('description') }}</textarea>
        </div>

        <div class="form-row" style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label for="exhibit_id">Exhibit *</label>
                <select name="exhibit_id" id="exhibit_id" class="form-control @error('exhibit_id') is-invalid @enderror" required>
                    <option value="">Select Exhibit</option>
                    @foreach($exhibits as $exhibit)
                        <option value="{{ $exhibit->id }}" {{ old('exhibit_id') == $exhibit->id ? 'selected' : '' }}>
                            {{ $exhibit->name }}
                        </option>
                    @endforeach
                </select>
                @error('exhibit_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="floor_plan_id">Floor Plan *</label>
                <select name="floor_plan_id" id="floor_plan_id" class="form-control @error('floor_plan_id') is-invalid @enderror" required>
                    <option value="">Select Floor Plan</option>
                </select>
                @error('floor_plan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <h4 style="margin-top: 20px; margin-bottom: 15px; color: #333;">Position (Model-Local Coordinates)</h4>
        <p style="color: #666; font-size: 14px; margin-bottom: 15px;">
            These coordinates represent where the QR code is located in the 3D floor model.
            You can copy these from an anchor's position or set them manually.
        </p>

        <div class="form-row" style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label for="position_x">Position X *</label>
                <input type="number" step="0.01" name="position_x" id="position_x"
                       class="form-control @error('position_x') is-invalid @enderror"
                       value="{{ old('position_x', 0) }}" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="position_y">Position Y *</label>
                <input type="number" step="0.01" name="position_y" id="position_y"
                       class="form-control @error('position_y') is-invalid @enderror"
                       value="{{ old('position_y', 0) }}" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="position_z">Position Z *</label>
                <input type="number" step="0.01" name="position_z" id="position_z"
                       class="form-control @error('position_z') is-invalid @enderror"
                       value="{{ old('position_z', 0) }}" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="rotation_y">Rotation Y (degrees)</label>
                <input type="number" step="1" name="rotation_y" id="rotation_y"
                       class="form-control"
                       value="{{ old('rotation_y', 0) }}" placeholder="0-360">
            </div>
        </div>

        <div class="form-group">
            <label for="anchor_id">Associated Anchor (Optional)</label>
            <select name="anchor_id" id="anchor_id" class="form-control">
                <option value="">None - Use custom position above</option>
            </select>
            <small class="form-text text-muted">
                If you select an anchor, you can copy its position using the button below.
            </small>
        </div>

        <button type="button" id="copyAnchorPosition" class="btn btn-secondary" style="margin-bottom: 20px;" disabled>
            Copy Position from Selected Anchor
        </button>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Create QR Code</button>
            <a href="{{ route('admin.qr-codes.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('exhibit_id').addEventListener('change', function() {
    const exhibitId = this.value;
    const floorPlanSelect = document.getElementById('floor_plan_id');
    const anchorSelect = document.getElementById('anchor_id');

    floorPlanSelect.innerHTML = '<option value="">Loading...</option>';
    anchorSelect.innerHTML = '<option value="">None</option>';

    if (exhibitId) {
        fetch(`/admin/ajax/exhibits/${exhibitId}/floor-plans`)
            .then(response => response.json())
            .then(data => {
                floorPlanSelect.innerHTML = '<option value="">Select Floor Plan</option>';
                data.forEach(fp => {
                    floorPlanSelect.innerHTML += `<option value="${fp.id}">Level ${fp.floor_level} - ${fp.name}</option>`;
                });
            });
    } else {
        floorPlanSelect.innerHTML = '<option value="">Select Floor Plan</option>';
    }
});

document.getElementById('floor_plan_id').addEventListener('change', function() {
    const floorPlanId = this.value;
    const anchorSelect = document.getElementById('anchor_id');
    const copyBtn = document.getElementById('copyAnchorPosition');

    anchorSelect.innerHTML = '<option value="">Loading...</option>';
    copyBtn.disabled = true;

    if (floorPlanId) {
        fetch(`/admin/ajax/floor-plans/${floorPlanId}/anchors`)
            .then(response => response.json())
            .then(data => {
                anchorSelect.innerHTML = '<option value="">None - Use custom position</option>';
                window.anchorData = {};
                data.forEach(anchor => {
                    window.anchorData[anchor.id] = anchor;
                    anchorSelect.innerHTML += `<option value="${anchor.id}">${anchor.anchor_name} (${anchor.anchor_type})</option>`;
                });
            });
    } else {
        anchorSelect.innerHTML = '<option value="">None</option>';
    }
});

document.getElementById('anchor_id').addEventListener('change', function() {
    const copyBtn = document.getElementById('copyAnchorPosition');
    copyBtn.disabled = !this.value;
});

document.getElementById('copyAnchorPosition').addEventListener('click', function() {
    const anchorId = document.getElementById('anchor_id').value;
    if (anchorId && window.anchorData && window.anchorData[anchorId]) {
        const anchor = window.anchorData[anchorId];
        document.getElementById('position_x').value = anchor.position_x;
        document.getElementById('position_y').value = anchor.position_y;
        document.getElementById('position_z').value = anchor.position_z;
    }
});
</script>
@endsection
