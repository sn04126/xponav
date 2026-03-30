<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful — XpoNav</title>
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
            background: #00c853;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
        }
        h1 { font-size: 26px; font-weight: 700; margin-bottom: 12px; color: #fff; }
        p  { font-size: 15px; color: #aaa; line-height: 1.6; margin-bottom: 32px; }
        .btn {
            display: block;
            background: linear-gradient(135deg, #00c853, #00897b);
            color: #fff;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.9; }
        .note { font-size: 13px; color: #666; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <h1>Payment Successful!</h1>
        <p>Your subscription has been activated. Switch back to the XpoNav app to enjoy full AR navigation access.</p>
        <a href="xponav://payment/success" class="btn">↩ Return to XpoNav App</a>
        <p class="note">If the button doesn't work, simply switch back to the app manually and tap "I've Completed Payment".</p>
    </div>
</body>
</html>
