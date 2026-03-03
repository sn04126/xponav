@extends('admin.layout')

@section('title', 'Edit Session')
@section('breadcrumb', 'Home / Sessions / Edit')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Session: {{ $session->name }}</h3>
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

    <form action="{{ route('admin.sessions.update', $session->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Session Name *</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $session->name) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="date">Date *</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $session->date ? $session->date->format('Y-m-d') : '') }}" required>
            </div>

            <div class="form-group">
                <label for="time">Time *</label>
                <input type="time" class="form-control" id="time" name="time" value="{{ old('time', $session->time) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $session->location) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="type">Session Type</label>
                <select class="form-control" id="type" name="type">
                    <option value="">Select Type</option>
                    <option value="workshop" {{ old('type', $session->type) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                    <option value="tour" {{ old('type', $session->type) == 'tour' ? 'selected' : '' }}>Tour</option>
                    <option value="lecture" {{ old('type', $session->type) == 'lecture' ? 'selected' : '' }}>Lecture</option>
                    <option value="demonstration" {{ old('type', $session->type) == 'demonstration' ? 'selected' : '' }}>Demonstration</option>
                    <option value="discussion" {{ old('type', $session->type) == 'discussion' ? 'selected' : '' }}>Discussion</option>
                </select>
            </div>

            <div class="form-group">
                <label for="hosted_by">Hosted By</label>
                <input type="text" class="form-control" id="hosted_by" name="hosted_by" value="{{ old('hosted_by', $session->hosted_by) }}">
            </div>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <input type="text" class="form-control" id="role" name="role" value="{{ old('role', $session->role) }}" placeholder="e.g., Curator, Artist, Educator">
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $session->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="image">Session Image</label>
            @if($session->image)
                <div style="margin-bottom: 10px;">
                    <img src="{{ asset('storage/' . $session->image) }}" alt="Current Image" style="max-width: 200px; border-radius: 6px;">
                </div>
            @endif
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <small style="color: #666;">Leave empty to keep current image</small>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Update Session</button>
            <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
