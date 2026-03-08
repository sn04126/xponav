<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $qrCode->name }}</title>
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

        .qr-card {
            width: 400px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 0 auto;
        }

        .qr-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .qr-header h1 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .qr-header p {
            font-size: 12px;
            opacity: 0.9;
        }

        .qr-body {
            padding: 30px;
            text-align: center;
        }

        .qr-image {
            width: 250px;
            height: 250px;
            margin: 0 auto 20px;
            background: white;
            padding: 10px;
            border: 2px solid #eee;
            border-radius: 12px;
        }

        .qr-image img {
            width: 100%;
            height: 100%;
        }

        .location-name {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .location-details {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .qr-code-text {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            color: #333;
            letter-spacing: 1px;
        }

        .qr-footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #eee;
        }

        .qr-footer p {
            font-size: 12px;
            color: #888;
        }

        .instructions {
            margin-top: 20px;
            padding: 15px;
            background: #e8f4fd;
            border-radius: 8px;
            text-align: left;
        }

        .instructions h3 {
            font-size: 14px;
            color: #0077b6;
            margin-bottom: 10px;
        }

        .instructions ol {
            font-size: 12px;
            color: #555;
            padding-left: 20px;
        }

        .instructions li {
            margin-bottom: 5px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .qr-card {
                box-shadow: none;
                border: 2px solid #ddd;
            }

            .no-print {
                display: none !important;
            }
        }

        .print-button {
            display: block;
            width: 400px;
            margin: 20px auto;
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
    </style>
</head>
<body>
    <div class="qr-card">
        <div class="qr-header">
            <h1>{{ $qrCode->exhibit->name ?? 'XpoNav Exhibition' }}</h1>
            <p>AR Navigation QR Code</p>
        </div>

        <div class="qr-body">
            <div class="qr-image">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($qrCode->code) }}"
                     alt="QR Code for {{ $qrCode->name }}">
            </div>

            <div class="location-name">{{ $qrCode->name }}</div>

            <div class="location-details">
                {{ $qrCode->floorPlan->name ?? 'Floor' }} | Level {{ $qrCode->floorPlan->floor_level ?? '?' }}
                @if($qrCode->description)
                    <br>{{ $qrCode->description }}
                @endif
            </div>

            <div class="qr-code-text">{{ $qrCode->code }}</div>

            <div class="instructions">
                <h3>How to Use</h3>
                <ol>
                    <li>Open the XpoNav app on your phone</li>
                    <li>Tap "Scan QR Code" in the AR view</li>
                    <li>Point your camera at this QR code</li>
                    <li>Select your destination and follow the arrows!</li>
                </ol>
            </div>
        </div>

        <div class="qr-footer">
            <p>Powered by XpoNav AR Navigation</p>
        </div>
    </div>

    <button class="print-button no-print" onclick="window.print()">
        Print This QR Code
    </button>

    <div class="no-print" style="text-align: center; margin-top: 10px;">
        <a href="{{ route('admin.qr-codes.index') }}" style="color: #667eea;">Back to QR Codes</a>
    </div>
</body>
</html>
