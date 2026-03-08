@extends('admin.layout')

@section('title', 'Subscription Management')
@section('breadcrumb', 'Dashboard / Subscriptions')

@section('content')
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <h4>Active Subscriptions</h4>
            <div class="value">{{ $stats['active'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <h4>Total Revenue</h4>
            <div class="value">${{ number_format($stats['revenue'] ?? 0, 2) }}</div>
        </div>
        <div class="stat-card">
            <h4>This Month</h4>
            <div class="value">{{ $stats['this_month'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <h4>Cancelled</h4>
            <div class="value">{{ $stats['cancelled'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="filter-bar">
            <div class="form-group">
                <label>Status</label>
                <select onchange="window.location.href='?status='+this.value" class="form-control">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="form-group">
                <label>Search</label>
                <input type="text" class="form-control" placeholder="Search by user email..." value="{{ request('search') }}" id="searchInput">
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card">
        <div class="card-header">
            <h3>Subscriptions</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                <tr>
                    <td>#{{ $sub->id }}</td>
                    <td>{{ $sub->user->name ?? 'N/A' }} <br><small style="color:#666;">{{ $sub->user->email ?? '' }}</small></td>
                    <td>{{ $sub->plan->name ?? 'N/A' }}</td>
                    <td>
                        @if($sub->status == 'active')
                            <span class="badge badge-success">Active</span>
                        @elseif($sub->status == 'cancelled')
                            <span class="badge badge-danger">Cancelled</span>
                        @elseif($sub->status == 'pending_verification')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-info">{{ ucfirst($sub->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $sub->start_date ? $sub->start_date->format('M d, Y') : '-' }}</td>
                    <td>{{ $sub->end_date ? $sub->end_date->format('M d, Y') : '-' }}</td>
                    <td>${{ number_format($sub->plan->total_fee ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #999;">No subscriptions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($subscriptions, 'links'))
        <div style="margin-top: 20px; text-align: center;">
            {{ $subscriptions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
@endsection
