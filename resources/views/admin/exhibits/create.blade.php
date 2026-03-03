@extends('admin.layout')

@section('title', 'Create New Exhibit')
@section('breadcrumb', 'Home / Exhibits / Create')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Add New Exhibit</h3>
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

    <form action="{{ route('admin.exhibits.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="title">Exhibit Title *</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="name">Exhibit Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ old('category') }}">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="">Select Status</option>
                    <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="ended" {{ old('status') == 'ended' ? 'selected' : '' }}>Ended</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="artist_name">Artist Name</label>
            <input type="text" class="form-control" id="artist_name" name="artist_name" value="{{ old('artist_name') }}">
        </div>

        <div class="form-group">
            <label for="artist_bio">Artist Bio</label>
            <textarea class="form-control" id="artist_bio" name="artist_bio" rows="3">{{ old('artist_bio') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="number" step="any" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}">
            </div>

            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="number" step="any" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="opening_time">Opening Time</label>
                <input type="time" class="form-control" id="opening_time" name="opening_time" value="{{ old('opening_time') }}">
            </div>

            <div class="form-group">
                <label for="closing_time">Closing Time</label>
                <input type="time" class="form-control" id="closing_time" name="closing_time" value="{{ old('closing_time') }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="ticket_price">Ticket Price</label>
                <input type="number" step="0.01" class="form-control" id="ticket_price" name="ticket_price" value="{{ old('ticket_price') }}">
            </div>

            <div class="form-group">
                <label for="rating">Rating (0-5)</label>
                <input type="number" step="0.1" min="0" max="5" class="form-control" id="rating" name="rating" value="{{ old('rating') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="image">Exhibit Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    Is Active
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="hidden" name="is_promoted" value="0">
                    <input type="checkbox" name="is_promoted" value="1" {{ old('is_promoted') ? 'checked' : '' }}>
                    Is Promoted
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Create Exhibit</button>
            <a href="{{ route('admin.exhibits.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
