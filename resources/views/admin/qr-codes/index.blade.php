@extends('admin.layout')

@section('title', 'QR Codes & Manual Codes')
@section('breadcrumb', 'QR Codes / List')

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h3 style="margin:0;">📍 Location QR Codes &amp; Manual Codes</h3>
        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <form method="GET" style="display:flex; gap:10px;">
                <select name="exhibit_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Exhibits</option>
                    @foreach($exhibits as $exhibit)
                        <option value="{{ $exhibit->id }}" {{ $exhibitId == $exhibit->id ? 'selected' : '' }}>
                            {{ $exhibit->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            @if($exhibitId)
            <a href="{{ route('admin.qr-codes.print-all', $exhibitId) }}" class="btn btn-success" target="_blank">
                🖨 Print All for Exhibit
            </a>
            @endif
            <a href="{{ route('admin.qr-codes.create') }}" class="btn btn-primary">+ New Location Code</a>
        </div>
    </div>

    {{-- Guide banner --}}
    <div style="background:#f0f7ff; border-left:4px solid #3b82f6; padding:14px 18px; margin:15px 15px 0;">
        <strong>📱 How to use these codes:</strong>
        <ul style="margin:6px 0 0 0; padding-left:20px; font-size:13px; color:#444;">
            <li><strong>QR Code</strong> — print and stick on the physical wall / display. Users scan with phone camera in the AR app.</li>
            <li><strong>Manual Code</strong> — the same code in text form (e.g. <code>XPONAV-GLBY-0001</code>). Users type it in the app's manual-entry field.</li>
            <li>The <strong>Starting Point</strong> (Grand Lobby Corridor, code <code>XPONAV-GLBY-0001</code>) should always be at the exhibition entrance.</li>
        </ul>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin: 15px;">✅ {{ session('success') }}</div>
    @endif

    <div style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>📍 Location Name</th>
                <th>🔑 Code (QR + Manual)</th>
                <th>Exhibit / Floor</th>
                <th>Position (X, Y, Z)</th>
                <th>Scans</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($qrCodes as $qrCode)
            <tr>
                <td style="color:#999; font-size:12px;">{{ $qrCode->id }}</td>
                <td>
                    <strong>{{ $qrCode->name }}</strong>
                    @if($qrCode->description)
                        <br><small style="color:#666;">{{ Str::limit($qrCode->description, 55) }}</small>
                    @endif
                </td>
                <td>
                    {{-- Prominent code display --}}
                    <div style="background:#1a1a2e; color:#00ff88; padding:7px 12px; border-radius:6px;
                                font-family:monospace; font-size:14px; font-weight:bold;
                                letter-spacing:1px; display:inline-block; margin-bottom:5px;">
                        {{ $qrCode->code }}
                    </div>
                    <br>
                    <small style="color:#888;">
                        📷 QR URL: <code style="font-size:10px;">{{ url('/api/qr/scan/'.$qrCode->code) }}</code>
                    </small>
                </td>
                <td>
                    <strong>{{ $qrCode->exhibit->name ?? 'N/A' }}</strong>
                    <br><small>{{ $qrCode->floorPlan->name ?? 'N/A' }}</small>
                    <br><small style="color:#888;">Level {{ $qrCode->floorPlan->floor_level ?? '?' }}</small>
                </td>
                <td style="font-family:monospace; font-size:11px; white-space:nowrap;">
                    X: {{ number_format($qrCode->position_x, 2) }}<br>
                    Y: {{ number_format($qrCode->position_y, 2) }}<br>
                    Z: {{ number_format($qrCode->position_z, 2) }}
                </td>
                <td style="text-align:center;">
                    <span style="font-weight:bold; color:#667eea; font-size:16px;">{{ $qrCode->scan_count }}</span>
                    <br><small style="color:#999;">scans</small>
                </td>
                <td>
                    @if($qrCode->is_active)
                        <span class="badge badge-success">✅ Active</span>
                    @else
                        <span class="badge badge-danger">⛔ Inactive</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex; gap:5px; flex-wrap:wrap;">
                        <a href="{{ route('admin.qr-codes.print', $qrCode) }}"
                           class="btn btn-sm btn-info" target="_blank" title="Print QR Code">
                            🖨 Print
                        </a>
                        <a href="{{ route('admin.qr-codes.edit', $qrCode) }}"
                           class="btn btn-sm btn-primary" title="Edit">
                            ✏️ Edit
                        </a>
                        <form action="{{ route('admin.qr-codes.regenerate', $qrCode) }}"
                              method="POST" style="display:inline;"
                              onsubmit="return confirm('Regenerate code? The old code will stop working immediately.');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning" title="Regenerate Code">
                                🔄 Regen
                            </button>
                        </form>
                        <form action="{{ route('admin.qr-codes.destroy', $qrCode) }}"
                              method="POST" style="display:inline;"
                              onsubmit="return confirm('Delete this QR code?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                🗑
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; padding:40px; color:#999;">
                    No QR codes found.
                    <a href="{{ route('admin.qr-codes.create') }}">Create your first location code →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    @if($qrCodes->hasPages())
        <div style="padding:15px;">
            {{ $qrCodes->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- Quick Code Reference Card --}}
<div class="card" style="margin-top:20px;">
    <div class="card-header"><h4 style="margin:0;">📋 Quick Code Reference — Give to Users or Print</h4></div>
    <div style="padding:20px;">
        <p style="color:#555; margin-bottom:15px;">
            Share these codes with visitors. They can type any code into the AR app's
            <strong>"Enter Code"</strong> field to set their starting position.
        </p>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px,1fr)); gap:12px;">
            @foreach($qrCodes->where('is_active', true) as $qrCode)
            <div style="border:2px dashed #ccc; border-radius:10px; padding:14px; text-align:center;">
                <div style="font-weight:bold; font-size:14px; margin-bottom:8px;">{{ $qrCode->name }}</div>
                <div style="background:#000; color:#00ff88; padding:10px 16px; border-radius:6px;
                            font-family:monospace; font-size:17px; font-weight:bold;
                            letter-spacing:2px; display:inline-block; margin-bottom:8px;">
                    {{ $qrCode->code }}
                </div>
                <div style="font-size:11px; color:#888;">
                    {{ $qrCode->exhibit->name ?? '' }}
                    @if($qrCode->floorPlan)
                    — {{ $qrCode->floorPlan->name }}
                    @endif
                </div>
                <a href="{{ route('admin.qr-codes.print', $qrCode) }}"
                   target="_blank"
                   style="display:inline-block; margin-top:8px; font-size:12px; color:#3b82f6;">
                    🖨 Print QR Poster
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
