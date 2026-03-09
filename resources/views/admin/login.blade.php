<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - XpoNav</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1D5C3C 0%, #2d7a54 50%, #1a4f34 100%);
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            background: white;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
        }

        .logo-icon svg {
            width: 40px;
            height: 40px;
            color: #1D5C3C;
        }

        .login-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.75);
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            padding: 40px;
        }

        .login-card h2 {
            font-size: 22px;
            font-weight: 600;
            color: #1D5C3C;
            margin-bottom: 6px;
        }

        .login-card .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            color: #333;
            background: #fafafa;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #1D5C3C;
            background: white;
            box-shadow: 0 0 0 4px rgba(29, 92, 60, 0.1);
        }

        .form-input::placeholder {
            color: #aaa;
        }

        .remember-row {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #1D5C3C;
            margin-right: 10px;
            cursor: pointer;
        }

        .remember-row label {
            font-size: 14px;
            color: #555;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1D5C3C, #2d7a54);
            color: white;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #174d32, #256b47);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(29, 92, 60, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc3545;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
        }

        .decoration {
            position: fixed;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            pointer-events: none;
        }

        .decoration-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -100px;
        }

        .decoration-2 {
            width: 300px;
            height: 300px;
            bottom: -80px;
            left: -80px;
        }

        .decoration-3 {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 10%;
        }
    </style>
</head>
<body>
    <div class="decoration decoration-1"></div>
    <div class="decoration decoration-2"></div>
    <div class="decoration decoration-3"></div>

    <div class="login-wrapper">
        <div class="login-header">
            <div class="logo-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
            </div>
            <h1>XpoNav</h1>
            <p>Exhibition Center AR Navigation</p>
        </div>

        <div class="login-card">
            <h2>Welcome Back</h2>
            <p class="subtitle">Sign in to access the admin dashboard</p>

            @if($errors->any())
                <div class="error-alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-input"
                        value="{{ old('email') }}"
                        placeholder="admin@xponav.com"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-input"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>

        <p class="login-footer">&copy; 2026 XpoNav. All rights reserved.</p>
    </div>
</body>
</html>
