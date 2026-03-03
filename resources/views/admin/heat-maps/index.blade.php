@extends('admin.layout')

@section('title', 'Heat Maps Analytics')
@section('breadcrumb', 'Analytics / Heat Maps')

@section('content')
<style>
    .heat-map-container {
        position: relative;
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        background: #f8f9fa;
        border-radius: 12px;
        overflow: hidden;
    }

    .heat-map-grid {
        position: relative;
        width: 100%;
        padding-bottom: 80%; /* Aspect ratio */
        background: linear-gradient(135deg, #e8f4f8 0%, #d0e8f0 100%);
    }

    .heat-point {
        position: absolute;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        pointer-events: auto;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .heat-point:hover {
        transform: translate(-50%, -50%) scale(1.3);
        z-index: 10;
    }

    .heat-point-tooltip {
        display: none;
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        margin-bottom: 8px;
        z-index: 100;
    }

    .heat-point:hover .heat-point-tooltip {
        display: block;
    }

    .filter-bar {
        display: flex;
        gap: 15px;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-bar select {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        min-width: 180px;
    }

    .period-buttons {
        display: flex;
        gap: 5px;
    }

    .period-btn {
        padding: 8px 16px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .period-btn.active {
        background: #1D5C3C;
        color: white;
        border-color: #1D5C3C;
    }

    .period-btn:hover:not(.active) {
        background: #f0f0f0;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .mini-stat {
        background: white;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .mini-stat .value {
        font-size: 28px;
        font-weight: 700;
        color: #1D5C3C;
    }

    .mini-stat .label {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .legend {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-top: 15px;
        padding: 15px;
        background: white;
        border-radius: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }

    .top-areas-list {
        background: white;
        border-radius: 8px;
        padding: 15px;
    }

    .top-area-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .top-area-item:last-child {
        border-bottom: none;
    }

    .area-rank {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #1D5C3C;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .area-info {
        flex: 1;
        margin-left: 12px;
    }

    .area-coords {
        font-weight: 500;
    }

    .area-visits {
        font-size: 12px;
        color: #666;
    }

    .floor-tabs {
        display: flex;
        gap: 5px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .floor-tab {
        padding: 10px 20px;
        border: 2px solid #ddd;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
    }

    .floor-tab.active {
        background: #1D5C3C;
        color: white;
        border-color: #1D5C3C;
    }

    .floor-tab:hover:not(.active) {
        border-color: #1D5C3C;
        color: #1D5C3C;
    }

    .no-data-message {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .no-data-message svg {
        width: 60px;
        height: 60px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .action-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
</style>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Heat Map Analytics</h3>
        <div class="action-bar" style="margin: 0;">
            <form action="{{ route('admin.heat-maps.aggregate') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="exhibit_id" value="{{ $selectedExhibitId }}">
                <button type="submit" class="btn btn-secondary btn-sm" title="Refresh heat map data">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh Data
                </button>
            </form>
            @if($heatMapData)
            <a href="{{ route('admin.heat-maps.export', ['exhibit_id' => $selectedExhibitId, 'floor_plan_id' => $selectedFloorPlanId, 'period' => $period]) }}" class="btn btn-info btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; vertical-align: middle;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export CSV
            </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div style="padding: 20px; border-bottom: 1px solid #eee;">
        <form method="GET" action="{{ route('admin.heat-maps.index') }}" class="filter-bar">
            <div>
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 5px;">Exhibit</label>
                <select name="exhibit_id" onchange="this.form.submit()">
                    @foreach($exhibits as $exhibit)
                        <option value="{{ $exhibit->id }}" {{ $selectedExhibitId == $exhibit->id ? 'selected' : '' }}>
                            {{ $exhibit->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 5px;">Floor</label>
                <select name="floor_plan_id" onchange="this.form.submit()">
                    @foreach($floorPlans as $floor)
                        <option value="{{ $floor->id }}" {{ $selectedFloorPlanId == $floor->id ? 'selected' : '' }}>
                            Level {{ $floor->floor_level }} - {{ $floor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 5px;">Time Period</label>
                <div class="period-buttons">
                    <button type="submit" name="period" value="today" class="period-btn {{ $period == 'today' ? 'active' : '' }}">Today</button>
                    <button type="submit" name="period" value="week" class="period-btn {{ $period == 'week' ? 'active' : '' }}">Week</button>
                    <button type="submit" name="period" value="month" class="period-btn {{ $period == 'month' ? 'active' : '' }}">Month</button>
                    <button type="submit" name="period" value="year" class="period-btn {{ $period == 'year' ? 'active' : '' }}">Year</button>
                </div>
            </div>
        </form>
    </div>

    @if($heatMapData && $heatMapData['points']->count() > 0)
        <!-- Stats Row -->
        <div style="padding: 20px;">
            <div class="stats-row">
                <div class="mini-stat">
                    <div class="value">{{ number_format($heatMapData['total_visits']) }}</div>
                    <div class="label">Total Visits</div>
                </div>
                <div class="mini-stat">
                    <div class="value">{{ number_format($heatMapData['unique_visitors']) }}</div>
                    <div class="label">Unique Visitors</div>
                </div>
                <div class="mini-stat">
                    <div class="value">{{ $heatMapData['points']->count() }}</div>
                    <div class="label">Active Zones</div>
                </div>
                <div class="mini-stat">
                    <div class="value">{{ $heatMapData['max_visits'] }}</div>
                    <div class="label">Peak Zone Visits</div>
                </div>
            </div>

            <!-- Heat Map Visualization -->
            <div class="row" style="display: flex; gap: 20px;">
                <div style="flex: 2;">
                    <h4 style="margin-bottom: 15px;">
                        {{ $heatMapData['floor_plan']->name ?? 'Floor' }} - Traffic Heat Map
                    </h4>
                    <div class="heat-map-container">
                        <div class="heat-map-grid" id="heatMapGrid">
                            @foreach($heatMapData['points'] as $point)
                                @php
                                    $hue = 120 - ($point['intensity'] * 120); // Green (120) to Red (0)
                                    $size = 20 + ($point['intensity'] * 30); // 20-50px
                                    $opacity = 0.4 + ($point['intensity'] * 0.5);
                                @endphp
                                <div class="heat-point"
                                     style="left: {{ $point['percent_x'] }}%;
                                            top: {{ $point['percent_z'] }}%;
                                            width: {{ $size }}px;
                                            height: {{ $size }}px;
                                            background: hsla({{ $hue }}, 80%, 50%, {{ $opacity }});">
                                    <div class="heat-point-tooltip">
                                        <strong>Position:</strong> ({{ number_format($point['x'], 1) }}, {{ number_format($point['z'], 1) }})<br>
                                        <strong>Visits:</strong> {{ $point['visits'] }}<br>
                                        <strong>Unique:</strong> {{ $point['unique_visitors'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend">
                        <span style="font-weight: 500; margin-right: 10px;">Intensity:</span>
                        <div class="legend-item">
                            <div class="legend-color" style="background: hsl(120, 80%, 50%);"></div>
                            <span>Low Traffic</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: hsl(60, 80%, 50%);"></div>
                            <span>Medium</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: hsl(0, 80%, 50%);"></div>
                            <span>High Traffic</span>
                        </div>
                    </div>
                </div>

                <!-- Top Areas -->
                <div style="flex: 1; min-width: 250px;">
                    <h4 style="margin-bottom: 15px;">Top Traffic Areas</h4>
                    <div class="top-areas-list">
                        @forelse($heatMapData['top_areas'] as $index => $area)
                            <div class="top-area-item">
                                <div class="area-rank">{{ $index + 1 }}</div>
                                <div class="area-info">
                                    <div class="area-coords">Zone ({{ number_format($area->grid_x, 1) }}, {{ number_format($area->grid_z, 1) }})</div>
                                    <div class="area-visits">{{ number_format($area->total_visits) }} visits | {{ number_format($area->unique_visitors) }} unique</div>
                                </div>
                            </div>
                        @empty
                            <p style="color: #999; text-align: center; padding: 20px;">No data available</p>
                        @endforelse
                    </div>

                    <!-- Daily Trend Mini Chart -->
                    @if($heatMapData['daily_trend']->count() > 0)
                        <h4 style="margin: 25px 0 15px;">Daily Trend</h4>
                        <div style="background: white; padding: 15px; border-radius: 8px;">
                            <div style="display: flex; align-items: flex-end; height: 100px; gap: 3px;">
                                @php
                                    $maxDailyVisits = $heatMapData['daily_trend']->max('visits') ?: 1;
                                @endphp
                                @foreach($heatMapData['daily_trend']->slice(-14) as $day)
                                    @php
                                        $height = ($day->visits / $maxDailyVisits) * 100;
                                    @endphp
                                    <div style="flex: 1; background: #1D5C3C; height: {{ $height }}%; border-radius: 2px 2px 0 0;"
                                         title="{{ $day->aggregation_date }}: {{ $day->visits }} visits"></div>
                                @endforeach
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 10px; color: #999;">
                                <span>{{ $heatMapData['daily_trend']->first()->aggregation_date ?? '' }}</span>
                                <span>{{ $heatMapData['daily_trend']->last()->aggregation_date ?? '' }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="no-data-message">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3>No Heat Map Data Available</h3>
            <p>Position tracking data will appear here once visitors start navigating through the exhibit using the AR app.</p>
            <p style="margin-top: 15px;">
                <form action="{{ route('admin.heat-maps.aggregate') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">Generate Sample Data</button>
                </form>
            </p>
        </div>
    @endif
</div>
@endsection
