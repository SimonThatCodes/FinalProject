<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        try {
            // Check if credentials are configured
            if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
                return response()->json([
                    'error' => 'Google OAuth not configured',
                    'message' => 'Please set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in your .env file. See GOOGLE_OAUTH_SETUP.md for instructions.'
                ], 500);
            }

            return Socialite::driver('google')
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Google OAuth redirect error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Google OAuth configuration error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            // Check if credentials are configured
            if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
                return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/login?error=' . urlencode('Google OAuth not configured. Please set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in your .env file.'));
            }

            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists
            $user = User::where('email', $googleUser->email)->first();
            
            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(16)), // Random password for OAuth users
                    'avatar' => $googleUser->avatar,
                    'verified' => true, // Google verified emails are considered verified
                ]);
            } else {
                // Update avatar if available
                if ($googleUser->avatar && !$user->avatar) {
                    $user->update(['avatar' => $googleUser->avatar]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/callback?token=' . $token . '&user=' . base64_encode(json_encode($user)));
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error: ' . $e->getMessage());
            return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/login?error=' . urlencode('Google authentication failed: ' . $e->getMessage()));
        }
    }

    public function getAuthUrl()
    {
        try {
            // Check if credentials are configured
            if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
                return response()->json([
                    'error' => 'Google OAuth not configured',
                    'message' => 'Please set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in your .env file. See GOOGLE_OAUTH_SETUP.md for instructions.'
                ], 500);
            }

            $url = Socialite::driver('google')
                ->redirect()
                ->getTargetUrl();
                
            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            Log::error('Google OAuth getAuthUrl error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Google OAuth configuration error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
