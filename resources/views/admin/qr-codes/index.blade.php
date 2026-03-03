@extends('admin.layout')

@section('title', 'QR Codes')
@section('breadcrumb', 'QR Codes / List')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Location QR Codes</h3>
        <div style="display: flex; gap: 10px;">
            <form method="GET" style="display: flex; gap: 10px;">
                <select name="exhibit_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Exhibits</option>
                    @foreach($exhibits as $exhibit)
                        <option value="{{ $exhibit->id }}" {{ $exhibitId == $exhibit->id ? 'selected' : '' }}>
                            {{ $exhibit->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.qr-codes.create') }}" class="btn btn-primary">+ Create QR Code</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>QR Code</th>
                <th>Name</th>
                <th>Exhibit</th>
                <th>Floor</th>
                <th>Position</th>
                <th>Scans</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($qrCodes as $qrCode)
            <tr>
                <td>
                    <code style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                        {{ $qrCode->code }}
                    </code>
                </td>
                <td>
                    <strong>{{ $qrCode->name }}</strong>
                    @if($qrCode->description)
                        <br><small style="color: #666;">{{ Str::limit($qrCode->description, 50) }}</small>
                    @endif
                </td>
                <td>{{ $qrCode->exhibit->name ?? 'N/A' }}</td>
                <td>
                    {{ $qrCode->floorPlan->name ?? 'N/A' }}
                    <br><small>Level {{ $qrCode->floorPlan->floor_level ?? '?' }}</small>
                </td>
                <td style="font-family: monospace; font-size: 11px;">
                    X: {{ number_format($qrCode->position_x, 2) }}<br>
                    Y: {{ number_format($qrCode->position_y, 2) }}<br>
                    Z: {{ number_format($qrCode->position_z, 2) }}
                </td>
                <td>
                    <span style="font-weight: bold; color: #667eea;">{{ $qrCode->scan_count }}</span>
                </td>
                <td>
                    @if($qrCode->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                        <a href="{{ route('admin.qr-codes.print', $qrCode) }}" class="btn btn-sm btn-info" target="_blank" title="Print">
                            Print
                        </a>
                        <a href="{{ route('admin.qr-codes.edit', $qrCode) }}" class="btn btn-sm btn-primary" title="Edit">
                            Edit
                        </a>
                        <form action="{{ route('admin.qr-codes.regenerate', $qrCode) }}" method="POST" style="display: inline;" onsubmit="return confirm('Regenerate QR code? Old code will stop working.');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning" title="Regenerate Code">
                                Regen
                            </button>
                        </form>
                        <form action="{{ route('admin.qr-codes.destroy', $qrCode) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this QR code?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                Del
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    No QR codes found.
                    <a href="{{ route('admin.qr-codes.create') }}">Create your first QR code</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($qrCodes->hasPages())
        <div style="padding: 15px;">
            {{ $qrCodes->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@if($exhibitId)
<div style="margin-top: 20px;">
    <a href="{{ route('admin.qr-codes.print-all', $exhibitId) }}" class="btn btn-primary" target="_blank">
        Print All QR Codes for This Exhibit
    </a>
</div>
@endif
@endsection
