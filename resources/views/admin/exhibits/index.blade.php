@extends('admin.layout')

@section('title', 'Exhibits Management')
@section('breadcrumb', 'Home / Exhibits')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>All Exhibits</h2>
    <a href="{{ route('admin.exhibits.create') }}" class="btn btn-primary">+ Add New Exhibit</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Location</th>
                <th>Floor Plans</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exhibits as $exhibit)
            <tr>
                <td>#{{ $exhibit->id }}</td>
                <td><strong>{{ $exhibit->name ?? $exhibit->title }}</strong></td>
                <td>{{ Str::limit($exhibit->description ?? 'N/A', 50) }}</td>
                <td>{{ $exhibit->location ?? 'N/A' }}</td>
                <td>{{ $exhibit->floor_plans_count ?? 0 }}</td>
                <td>
                    @if($exhibit->is_active ?? true)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.exhibits.edit', $exhibit->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <a href="{{ route('admin.floor-plans.index', ['exhibit_id' => $exhibit->id]) }}" class="btn btn-sm btn-secondary">Floor Plans</a>
                    <form action="{{ route('admin.exhibits.destroy', $exhibit->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this exhibit?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px;">
                    No exhibits found. <a href="{{ route('admin.exhibits.create') }}">Create your first exhibit</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($exhibits->hasPages())
<div style="margin-top: 20px;">
    {{ $exhibits->links() }}
</div>
@endif
@endsection
