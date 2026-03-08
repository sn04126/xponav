@extends('admin.layout')

@section('title', 'Navigation Analytics')
@section('breadcrumb', 'Dashboard / Navigation Analytics')

@section('content')
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total Sessions</h4>
            <div class="value">{{ $stats['total_sessions'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <h4>Active Now</h4>
            <div class="value">{{ $stats['active_sessions'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <h4>Unique Users</h4>
            <div class="value">{{ $stats['unique_users'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <h4>Avg Distance</h4>
            <div class="value">{{ number_format($stats['avg_distance'] ?? 0, 1) }}m</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="filter-bar">
            <div class="form-group">
                <label>Period</label>
                <select onchange="window.location.href='?period='+this.value" class="form-control">
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('period', 'week') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select onchange="window.location.href='?period={{ request('period', 'week') }}&status='+this.value" class="form-control">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Popular Destinations -->
    @if(!empty($stats['popular_destinations']))
    <div class="card">
        <div class="card-header">
            <h3>Popular Destinations</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Destination</th>
                    <th>Visits</th>
                    <th>Popularity</th>
                </tr>
            </thead>
            <tbody>
                @php $maxVisits = collect($stats['popular_destinations'])->max() ?: 1; @endphp
                @foreach($stats['popular_destinations'] as $dest => $count)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $dest }}</td>
                    <td>{{ $count }}</td>
                    <td>
                        <div style="background: #e0e0e0; border-radius: 4px; height: 20px; width: 200px; position: relative;">
                            <div style="background: #1D5C3C; border-radius: 4px; height: 100%; width: {{ ($count / $maxVisits) * 100 }}%;"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Recent Sessions -->
    <div class="card">
        <div class="card-header">
            <h3>Recent Navigation Sessions</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Started</th>
                    <th>Duration</th>
                    <th>Destinations</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr>
                    <td>#{{ $session->id }}</td>
                    <td>{{ $session->user->name ?? 'Anonymous' }}</td>
                    <td>{{ $session->started_at ? $session->started_at->format('M d, H:i') : '-' }}</td>
                    <td>
                        @if($session->started_at && $session->ended_at)
                            {{ $session->started_at->diffForHumans($session->ended_at, true) }}
                        @elseif($session->status == 'active')
                            <span class="badge badge-success">In Progress</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ count($session->destinations_visited ?? []) }}</td>
                    <td>
                        @if($session->status == 'active')
                            <span class="badge badge-success">Active</span>
                        @elseif($session->status == 'completed')
                            <span class="badge badge-info">Completed</span>
                        @else
                            <span class="badge badge-warning">{{ ucfirst($session->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #999;">No navigation sessions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($sessions, 'links'))
        <div style="margin-top: 20px; text-align: center;">
            {{ $sessions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
@endsection
