<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $code = rand(100000, 999999);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'username' => explode('@', $validated['email'])[0] . rand(100, 999), // Temporary username
            'role' => 'user',
            'status' => 'pending',
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        // Log verification code to file for demo purposes
        $this->logVerificationCode($user->email, $code, 'Registration');

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerificationCodeMail($code));

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please verify your email.',
            'user' => $user
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified.'
            ], 400);
        }

        if ($user->verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.'
            ], 400);
        }

        if (now()->greaterThan($user->verification_code_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code expired.'
            ], 400);
        }

        $user->update([
            'email_verified_at' => Carbon::now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
            'status' => 'active',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function resendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $code = rand(100000, 999999);

        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        // Log verification code to file for demo purposes
        $this->logVerificationCode($user->email, $code, 'Resend Verification');

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerificationCodeMail($code));

        return response()->json([
            'success' => true,
            'message' => 'Verification code resent successfully.'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        if (! $user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email address.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        // Rate limit: 5 attempts per minute per IP
        $key = 'forgot-password:' . $request->ip();
        if (\Illuminate\Support\Facades\Cache::has($key) && \Illuminate\Support\Facades\Cache::get($key) >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Too many password reset attempts. Please try again later.'
            ], 429);
        }
        \Illuminate\Support\Facades\Cache::put($key, (\Illuminate\Support\Facades\Cache::get($key, 0)) + 1, 60);

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        $code = rand(100000, 999999);

        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        // Log verification code to file for demo purposes
        $this->logVerificationCode($user->email, $code, 'Password Reset');

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordCodeMail($code));

        return response()->json([
            'success' => true,
            'message' => 'Password reset code sent to your email.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user->verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.'
            ], 400);
        }

        if (now()->greaterThan($user->verification_code_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code expired.'
            ], 400);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now login.'
        ]);
    }

    public function updateProfile(Request $request)
    {
       $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:255',
            'country' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
            'address' => 'sometimes|nullable|string|max:255',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle profile image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->image);
            }

            $path = $request->file('image')->store('profile_images', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (! \Illuminate\Support\Facades\Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 400);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['new_password']),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Return the authenticated user's profile.
     * GET /api/me
     */
    public function me(Request $request)
    {
        $user = $request->user()->fresh(); // reload from DB to get latest data

        return response()->json([
            'success' => true,
            'user'    => $user,
        ]);
    }

    /**
     * Log verification code to file for demo/testing purposes
     * File location: storage/logs/verification_codes.txt
     */
    private function logVerificationCode($email, $code, $type)
    {
        $logFile = storage_path('logs/verification_codes.txt');

        // Ensure directory exists
        $logDir = dirname($logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d H:i:s');
        $expiresAt = now()->addMinutes(10)->format('Y-m-d H:i:s');

        $content = str_repeat('=', 60) . "\n";
        $content .= "[$timestamp] $type\n";
        $content .= str_repeat('=', 60) . "\n";
        $content .= "Email: $email\n";
        $content .= "Verification Code: $code\n";
        $content .= "Expires at: $expiresAt (10 minutes)\n";
        $content .= str_repeat('-', 60) . "\n\n";

        file_put_contents($logFile, $content, FILE_APPEND);

        \Illuminate\Support\Facades\Log::info("Verification code logged for demo: $email - Code: $code");
    }
}
