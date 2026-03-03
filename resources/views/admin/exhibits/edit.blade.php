@extends('admin.layout')

@section('title', 'Edit Exhibit')
@section('breadcrumb', 'Home / Exhibits / Edit')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Exhibit: {{ $exhibit->name }}</h3>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.exhibits.update', $exhibit->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Exhibit Title *</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $exhibit->title) }}" required>
        </div>

        <div class="form-group">
            <label for="name">Exhibit Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $exhibit->name) }}">
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $exhibit->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $exhibit->location) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $exhibit->category) }}">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="">Select Status</option>
                    <option value="upcoming" {{ old('status', $exhibit->status) == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ old('status', $exhibit->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="ended" {{ old('status', $exhibit->status) == 'ended' ? 'selected' : '' }}>Ended</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="artist_name">Artist Name</label>
            <input type="text" class="form-control" id="artist_name" name="artist_name" value="{{ old('artist_name', $exhibit->artist_name) }}">
        </div>

        <div class="form-group">
            <label for="artist_bio">Artist Bio</label>
            <textarea class="form-control" id="artist_bio" name="artist_bio" rows="3">{{ old('artist_bio', $exhibit->artist_bio) }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="number" step="any" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $exhibit->latitude) }}">
            </div>

            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="number" step="any" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $exhibit->longitude) }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="opening_time">Opening Time</label>
                <input type="time" class="form-control" id="opening_time" name="opening_time" value="{{ old('opening_time', $exhibit->opening_time) }}">
            </div>

            <div class="form-group">
                <label for="closing_time">Closing Time</label>
                <input type="time" class="form-control" id="closing_time" name="closing_time" value="{{ old('closing_time', $exhibit->closing_time) }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="ticket_price">Ticket Price</label>
                <input type="number" step="0.01" class="form-control" id="ticket_price" name="ticket_price" value="{{ old('ticket_price', $exhibit->ticket_price) }}">
            </div>

            <div class="form-group">
                <label for="rating">Rating (0-5)</label>
                <input type="number" step="0.1" min="0" max="5" class="form-control" id="rating" name="rating" value="{{ old('rating', $exhibit->rating) }}">
            </div>
        </div>

        <div class="form-group">
            <label for="image">Exhibit Image</label>
            @if($exhibit->image)
                <div style="margin-bottom: 10px;">
                    <img src="{{ asset('storage/' . $exhibit->image) }}" alt="Current Image" style="max-width: 200px; border-radius: 6px;">
                </div>
            @endif
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <small style="color: #666;">Leave empty to keep current image</small>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $exhibit->is_active) ? 'checked' : '' }}>
                    Is Active
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="hidden" name="is_promoted" value="0">
                    <input type="checkbox" name="is_promoted" value="1" {{ old('is_promoted', $exhibit->is_promoted) ? 'checked' : '' }}>
                    Is Promoted
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Update Exhibit</button>
            <a href="{{ route('admin.exhibits.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
