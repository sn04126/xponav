<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All QR Codes - {{ $exhibit->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .qr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .qr-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            page-break-inside: avoid;
        }

        .qr-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            text-align: center;
        }

        .qr-header h3 {
            font-size: 14px;
            font-weight: 600;
        }

        .qr-body {
            padding: 20px;
            text-align: center;
        }

        .qr-image {
            width: 180px;
            height: 180px;
            margin: 0 auto 15px;
            background: white;
            padding: 8px;
            border: 2px solid #eee;
            border-radius: 8px;
        }

        .qr-image img {
            width: 100%;
            height: 100%;
        }

        .location-name {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .floor-info {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        .qr-code-text {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
            color: #333;
        }

        @media print {
            body {
                background: white;
                padding: 10px;
            }

            .qr-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .qr-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .no-print {
                display: none !important;
            }
        }

        .print-button {
            display: block;
            width: 300px;
            margin: 30px auto;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .print-button:hover {
            background: #5a6fd6;
        }

        .back-link {
            text-align: center;
            margin-top: 10px;
        }

        .back-link a {
            color: #667eea;
        }

        .empty-message {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1 class="no-print">{{ $exhibit->name }} - All QR Codes</h1>

    @if($qrCodes->count() > 0)
        <div class="qr-grid">
            @foreach($qrCodes as $qrCode)
            <div class="qr-card">
                <div class="qr-header">
                    <h3>{{ $exhibit->name }}</h3>
                </div>
                <div class="qr-body">
                    <div class="qr-image">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrCode->code) }}"
                             alt="QR Code">
                    </div>
                    <div class="location-name">{{ $qrCode->name }}</div>
                    <div class="floor-info">
                        {{ $qrCode->floorPlan->name ?? 'Floor' }} | Level {{ $qrCode->floorPlan->floor_level ?? '?' }}
                    </div>
                    <div class="qr-code-text">{{ $qrCode->code }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <button class="print-button no-print" onclick="window.print()">
            Print All QR Codes ({{ $qrCodes->count() }} total)
        </button>
    @else
        <div class="empty-message">
            <h2>No QR Codes Found</h2>
            <p>No active QR codes have been created for this exhibit yet.</p>
        </div>
    @endif

    <div class="back-link no-print">
        <a href="{{ route('admin.qr-codes.index', ['exhibit_id' => $exhibit->id]) }}">Back to QR Codes</a>
    </div>
</body>
</html>
