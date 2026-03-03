@extends('admin.layout')

@section('title', 'Dashboard')
@section('breadcrumb', 'Home / Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <h4>Total Exhibits</h4>
        <div class="value">{{ $stats['exhibits'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <h4>Floor Plans</h4>
        <div class="value">{{ $stats['floor_plans'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <h4>AR Anchors</h4>
        <div class="value">{{ $stats['ar_anchors'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <h4>Total Users</h4>
        <div class="value">{{ $stats['users'] ?? 0 }}</div>
    </div>
</div>

<!-- Visit Analytics Summary -->
<div class="stats-grid" style="margin-top: 20px;">
    <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h4 style="color: rgba(255,255,255,0.9);">Total Visits</h4>
        <div class="value">{{ $visit_stats['total_visits'] ?? 0 }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
        <h4 style="color: rgba(255,255,255,0.9);">Today's Visits</h4>
        <div class="value">{{ $visit_stats['today_visits'] ?? 0 }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); color: white;">
        <h4 style="color: rgba(255,255,255,0.9);">This Week</h4>
        <div class="value">{{ $visit_stats['week_visits'] ?? 0 }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #4568dc 0%, #b06ab3 100%); color: white;">
        <h4 style="color: rgba(255,255,255,0.9);">Avg. Duration</h4>
        <div class="value">{{ gmdate("i:s", $visit_stats['avg_duration'] ?? 0) }}</div>
    </div>
</div>

<div class="row" style="display: flex; gap: 20px; margin-top: 20px;">
    <!-- Most Visited Exhibits -->
    <div class="card" style="flex: 1;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Most Visited Exhibits</h3>
            <span class="badge badge-primary">This Week</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Exhibit</th>
                    <th>Visits</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody>
                @forelse($most_visited ?? [] as $index => $visit)
                <tr>
                    <td>
                        @if($index == 0)
                            <span style="font-size: 20px;">🥇</span>
                        @elseif($index == 1)
                            <span style="font-size: 20px;">🥈</span>
                        @elseif($index == 2)
                            <span style="font-size: 20px;">🥉</span>
                        @else
                            #{{ $index + 1 }}
                        @endif
                    </td>
                    <td>{{ $visit->exhibit->name ?? 'Unknown' }}</td>
                    <td><strong>{{ $visit->visit_count }}</strong></td>
                    <td>
                        <span style="color: #38ef7d;">▲</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #888;">
                        No visit data yet. Visits will appear here once users start navigating.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Daily Visits Chart Placeholder -->
    <div class="card" style="flex: 1;">
        <div class="card-header">
            <h3>Visit Trend (Last 7 Days)</h3>
        </div>
        <div style="padding: 20px;">
            <div style="display: flex; align-items: flex-end; height: 200px; gap: 10px;">
                @foreach($daily_visits ?? [] as $day)
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 100%; border-radius: 4px 4px 0 0; min-height: 10px; height: {{ max(10, ($day->count / max(1, $max_daily_visits)) * 150) }}px;"></div>
                    <small style="margin-top: 5px; color: #888;">{{ \Carbon\Carbon::parse($day->visit_date)->format('D') }}</small>
                    <small style="font-weight: bold;">{{ $day->count }}</small>
                </div>
                @endforeach
                @if(empty($daily_visits) || count($daily_visits) == 0)
                <div style="width: 100%; text-align: center; color: #888; padding: 60px 0;">
                    No visit data for the past 7 days
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Recent Exhibits</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Views</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recent_exhibits ?? [] as $exhibit)
            <tr>
                <td>#{{ $exhibit->id }}</td>
                <td>{{ $exhibit->name }}</td>
                <td>{{ $exhibit->location ?? 'N/A' }}</td>
                <td>{{ $exhibit->view_count ?? 0 }}</td>
                <td>
                    @if($exhibit->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
                <td>{{ $exhibit->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('admin.exhibits.edit', $exhibit->id) }}" class="btn btn-sm btn-primary">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px;">No exhibits found. <a href="{{ route('admin.exhibits.create') }}">Create one</a></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card">
    <div class="card-header">
        <h3>Recent Users</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recent_users ?? [] as $user)
            <tr>
                <td>#{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                    @if($user->email_verified_at)
                        <span class="badge badge-success">Verified</span>
                    @else
                        <span class="badge badge-warning">Unverified</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px;">No users found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
