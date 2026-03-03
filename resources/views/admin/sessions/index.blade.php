@extends('admin.layout')

@section('title', 'Sessions Management')
@section('breadcrumb', 'Home / Sessions')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>All Interactive Sessions</h2>
    <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary">+ Add New Session</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Date & Time</th>
                <th>Location</th>
                <th>Type</th>
                <th>Hosted By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
            <tr>
                <td>#{{ $session->id }}</td>
                <td><strong>{{ $session->name }}</strong></td>
                <td>
                    {{ $session->date ? $session->date->format('M d, Y') : 'N/A' }}<br>
                    <small style="color: #666;">{{ $session->time ?? 'N/A' }}</small>
                </td>
                <td>{{ $session->location ?? 'N/A' }}</td>
                <td>
                    @if($session->type)
                        <span class="badge badge-success">{{ ucfirst($session->type) }}</span>
                    @else
                        <span class="badge badge-warning">N/A</span>
                    @endif
                </td>
                <td>{{ $session->hosted_by ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.sessions.edit', $session->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <form action="{{ route('admin.sessions.destroy', $session->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this session?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px;">
                    No sessions found. <a href="{{ route('admin.sessions.create') }}">Create your first session</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($sessions->hasPages())
<div style="margin-top: 20px;">
    {{ $sessions->links() }}
</div>
@endif
@endsection
