<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled — XpoNav</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0d0d0d;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 24px;
        }
        .card {
            background: #1a1a1a;
            border-radius: 20px;
            padding: 48px 36px;
            max-width: 400px;
            width: 100%;
            border: 1px solid #2a2a2a;
        }
        .icon {
            width: 80px; height: 80px;
            background: #e53935;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
        }
        h1 { font-size: 26px; font-weight: 700; margin-bottom: 12px; }
        p  { font-size: 15px; color: #aaa; line-height: 1.6; margin-bottom: 32px; }
        .btn {
            display: block;
            background: #2a2a2a;
            color: #fff;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            border: 1px solid #444;
            transition: background 0.2s;
        }
        .btn:hover { background: #333; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✕</div>
        <h1>Payment Cancelled</h1>
        <p>No charge was made. Switch back to the XpoNav app to choose a plan and try again.</p>
        <a href="xponav://payment/cancel" class="btn">↩ Return to XpoNav App</a>
    </div>
</body>
</html>
