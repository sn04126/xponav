<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider
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
     */
    public function handleProviderCallback($provider)
    {
        try {
            $validProviders = ['google', 'facebook', 'apple'];
            
            if (!in_array($provider, $validProviders)) {
                return response()->json(['error' => 'Invalid provider'], 400);
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
                        'password' => \Illuminate\Support\Facades\Hash::make(rand(100000, 999999)), // Random password
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

            return response()->json([
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
}
