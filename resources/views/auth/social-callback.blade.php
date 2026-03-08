<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XpoNav - Login Successful</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f1a15; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1a2e23; border-radius: 16px; padding: 40px; max-width: 420px; width: 90%; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .icon { width: 80px; height: 80px; background: #1D5C3C; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 36px; }
        h1 { font-size: 24px; margin-bottom: 8px; color: #4ade80; }
        .subtitle { color: #9ca3af; margin-bottom: 24px; }
        .info { background: #0f1a15; border-radius: 10px; padding: 16px; margin-bottom: 20px; text-align: left; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #1a2e23; }
        .info-row:last-child { border-bottom: none; }
        .label { color: #9ca3af; font-size: 13px; }
        .value { color: #fff; font-size: 13px; font-weight: 600; }
        .provider-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 16px; }
        .provider-google { background: #4285f422; color: #4285f4; }
        .provider-facebook { background: #1877f222; color: #1877f2; }
        .continue-btn { display: block; width: 100%; padding: 14px; background: #1D5C3C; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 20px; text-decoration: none; transition: background 0.3s; }
        .continue-btn:hover { background: #2d7a54; }
        .status-text { font-size: 14px; color: #4ade80; margin-top: 16px; }
        .loading-dots { display: inline-block; }
        .loading-dots::after { content: ''; animation: dots 1.5s steps(4, end) infinite; }
        @keyframes dots { 0% { content: ''; } 25% { content: '.'; } 50% { content: '..'; } 75% { content: '...'; } }
        .avatar { width: 64px; height: 64px; border-radius: 50%; margin: 0 auto 16px; border: 3px solid #1D5C3C; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">&#10003;</div>
        <h1>Login Successful!</h1>
        <span class="provider-badge provider-{{ $provider }}">{{ ucfirst($provider) }}</span>

        @if($user->avatar)
            <img src="{{ $user->avatar }}" alt="Profile" class="avatar">
        @endif

        <p class="subtitle">Welcome, {{ $user->name }}!</p>

        <div class="info">
            <div class="info-row">
                <span class="label">Name</span>
                <span class="value">{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Email</span>
                <span class="value">{{ $user->email }}</span>
            </div>
            <div class="info-row">
                <span class="label">Signed in via</span>
                <span class="value">{{ ucfirst($provider) }}</span>
            </div>
        </div>

        <p class="status-text" id="statusText">
            Opening XpoNav app<span class="loading-dots"></span>
        </p>

        <a href="{{ $deepLink }}" class="continue-btn" id="continueBtn">
            Continue with App
        </a>

        <p style="font-size: 12px; color: #6b7280; margin-top: 12px;">
            You can close this window after the app opens.
        </p>
    </div>

    <script>
        // Try deep link automatically after 1 second
        setTimeout(function() {
            window.location.href = "{{ $deepLink }}";
        }, 1000);

        // After 4 seconds, if still on this page, show fallback message
        setTimeout(function() {
            document.getElementById('statusText').innerHTML = 'Login successful! You can now return to the app.';
        }, 4000);
    </script>
</body>
</html>
