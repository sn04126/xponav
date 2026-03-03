@extends('admin.layout')

@section('title', 'Users Management')
@section('breadcrumb', 'Home / Users')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>All Users</h2>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>#{{ $user->id }}</td>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->username ?? 'N/A' }}</td>
                <td>{{ $user->phone ?? 'N/A' }}</td>
                <td>
                    @if($user->role)
                        <span class="badge badge-success">{{ ucfirst($user->role) }}</span>
                    @else
                        <span class="badge badge-warning">User</span>
                    @endif
                </td>
                <td>
                    @if($user->status == 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($user->status == 'inactive')
                        <span class="badge badge-warning">Inactive</span>
                    @else
                        <span class="badge badge-success">Active</span>
                    @endif
                </td>
                <td>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-secondary">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 40px;">
                    No users found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
<div style="margin-top: 20px;">
    {{ $users->links() }}
</div>
@endif
@endsection
