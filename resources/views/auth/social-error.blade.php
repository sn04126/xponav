<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XpoNav - Login Failed</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f1a15; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1a2e23; border-radius: 16px; padding: 40px; max-width: 420px; width: 90%; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .icon { width: 80px; height: 80px; background: #5c1d1d; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 36px; }
        h1 { font-size: 24px; margin-bottom: 8px; color: #f87171; }
        .subtitle { color: #9ca3af; margin-bottom: 24px; font-size: 14px; line-height: 1.5; }
        .error-box { background: #1c1010; border: 1px solid #5c1d1d; border-radius: 10px; padding: 16px; margin-bottom: 20px; text-align: left; }
        .error-box .label { color: #f87171; font-size: 12px; font-weight: 600; margin-bottom: 6px; }
        .error-box .value { color: #fca5a5; font-size: 13px; word-break: break-word; }
        .btn { display: block; width: 100%; padding: 14px; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 12px; text-decoration: none; text-align: center; transition: background 0.3s; }
        .btn-retry { background: #1D5C3C; color: white; }
        .btn-retry:hover { background: #2d7a54; }
        .btn-back { background: #374151; color: white; }
        .btn-back:hover { background: #4b5563; }
        .hint { font-size: 12px; color: #6b7280; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">&#10007;</div>
        <h1>Login Failed</h1>
        <p class="subtitle">Something went wrong during authentication. Please try again.</p>

        <div class="error-box">
            <div class="label">Error Details</div>
            <div class="value">{{ $message }}</div>
        </div>

        <a href="{{ url('/auth/google') }}" class="btn btn-retry">Try Google Sign-In</a>
        <a href="{{ url('/auth/facebook') }}" class="btn btn-retry" style="background: #1877f2;">Try Facebook Sign-In</a>
        <a href="{{ $deepLink }}" class="btn btn-back">Return to App</a>

        <p class="hint">If the app doesn't open, please switch back to XpoNav manually.</p>
    </div>
</body>
</html>
