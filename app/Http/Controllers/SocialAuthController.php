<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider
     * Called when Unity opens browser to /auth/{provider}
     */
    public function redirectToProvider($provider)
    {
        $validProviders = ['google', 'facebook', 'apple'];

        if (!in_array($provider, $validProviders)) {
            return response()->json(['error' => 'Invalid provider'], 400);
        }

        return \Laravel\Socialite\Facades\Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Handle callback from social provider
     * After OAuth completes, redirects to Unity deep link with token
     */
    public function handleProviderCallback($provider)
    {
        try {
            $validProviders = ['google', 'facebook', 'apple'];

            if (!in_array($provider, $validProviders)) {
                return $this->errorRedirect('Invalid provider');
            }

            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->stateless()->user();

            // Find or create user
            $user = \App\Models\User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$user) {
                // Check if user exists with this email
                $user = \App\Models\User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    // Link social account to existing user
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'provider_token' => $socialUser->token,
                        'avatar' => $socialUser->getAvatar(),
                    ]);
                } else {
                    // Create new user
                    $user = \App\Models\User::create([
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        'username' => explode('@', $socialUser->getEmail())[0] . rand(100, 999),
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'provider_token' => $socialUser->token,
                        'avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(),
                        'role' => 'user',
                        'status' => 'active',
                        'password' => \Illuminate\Support\Facades\Hash::make(rand(100000, 999999)),
                    ]);
                }
            } else {
                // Update token
                $user->update([
                    'provider_token' => $socialUser->token,
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            // Create Sanctum token
            $token = $user->createToken('social_auth_token')->plainTextToken;

            // Build user data for response
            $userInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'membership_tier' => $user->membership_tier,
                'membership_expiry' => $user->membership_expiry,
            ];

            // Check request source
            $source = request()->query('source', session('auth_source', 'unity'));

            if ($source === 'web') {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user,
                ]);
            }

            // Try deep link for Unity, with web fallback page
            $userData = base64_encode(json_encode($userInfo));

            $deepLink = "xponav://auth/callback?" . http_build_query([
                'token' => $token,
                'user' => $userData,
                'provider' => $provider,
            ]);

            // Return a page that tries deep link AND shows success info
            return response()->view('auth.social-callback', [
                'deepLink' => $deepLink,
                'user' => $user,
                'token' => $token,
                'provider' => $provider,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Social auth failed: ' . $e->getMessage(), [
                'provider' => $provider,
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorRedirect('Authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle social auth callback as JSON (for web/API clients)
     */
    public function handleProviderCallbackJson($provider)
    {
        try {
            $validProviders = ['google', 'facebook', 'apple'];

            if (!in_array($provider, $validProviders)) {
                return response()->json(['error' => 'Invalid provider'], 400);
            }

            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->stateless()->user();

            $user = $this->findOrCreateSocialUser($socialUser, $provider);
            $token = $user->createToken('social_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find or create user from social provider data
     */
    private function findOrCreateSocialUser($socialUser, $provider)
    {
        $user = \App\Models\User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            $user = \App\Models\User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } else {
                $user = \App\Models\User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'username' => explode('@', $socialUser->getEmail())[0] . rand(100, 999),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                    'role' => 'user',
                    'status' => 'active',
                    'password' => \Illuminate\Support\Facades\Hash::make(rand(100000, 999999)),
                ]);
            }
        } else {
            $user->update([
                'provider_token' => $socialUser->token,
                'avatar' => $socialUser->getAvatar(),
            ]);
        }

        return $user;
    }

    /**
     * Show error page with option to retry or return to app
     */
    private function errorRedirect($message)
    {
        $deepLink = "xponav://auth/callback?" . http_build_query([
            'error' => $message,
        ]);

        return response()->view('auth.social-error', [
            'message' => $message,
            'deepLink' => $deepLink,
        ]);
    }
}
