<?php

namespace User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider for authentication
     */
    public function redirectToProvider($provider, Request $req)
    {
        try {
            $state = $req->query('state') ?: uniqid();
            session(['oauth_state' => $state]);
            
            return Socialite::driver($provider)
                ->with(['state' => $state])
                ->redirect();
        } catch (Exception $e) {
            \Log::error("OAuth redirect error for {$provider}: " . $e->getMessage());
            return redirect()->back()->with('error', "Failed to redirect to {$provider}");
        }
    }

    /**
     * Handle OAuth callback from social provider
     */
    public function handleProviderCallback($provider, Request $req)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            $state = $req->query('state') ?: session('oauth_state');

            // Extract user data
            $email = $socialUser->getEmail();
            $name = $socialUser->getName();
            $providerUserId = $socialUser->getId();

            if (!$email) {
                return $this->oauthErrorResponse('email', "Could not retrieve email from {$provider}");
            }

            // Find or create user
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name ?? 'User',
                    'username' => $this->generateUniqueUsername($name),
                    'password' => bcrypt(bin2hex(random_bytes(32))),
                    'is_email_verified' => 1,
                    'email_verified_at' => now(),
                    'is_active' => 1,
                    'type' => 'social_' . $provider,
                ]
            );

            // Update user info if it was created or needs updating
            if (!$user->is_email_verified) {
                $user->update([
                    'is_email_verified' => 1,
                    'email_verified_at' => now(),
                ]);
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);
            $expiresIn = auth('api')->factory()->getTTL() * 60;

            // Return callback view with token
            return view('user:social_callback', [
                'state' => $state,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => $expiresIn,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                ],
                'frontend_url' => config('app.frontend_url'),
            ]);

        } catch (Exception $e) {
            \Log::error("OAuth callback error for {$provider}: " . $e->getMessage());
            return $this->oauthErrorResponse('provider', "Authentication with {$provider} failed: " . $e->getMessage());
        }
    }

    /**
     * Return OAuth error response
     */
    private function oauthErrorResponse($field, $message)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => [$field => $message]
        ], 401);
    }

    /**
     * Google OAuth2 authentication endpoint
     */
    public function googleAuth(Request $request)
    {
        return $this->handleSocialAuth('google', $request);
    }

    /**
     * Facebook OAuth2 authentication endpoint
     */
    public function facebookAuth(Request $request)
    {
        return $this->handleSocialAuth('facebook', $request);
    }

    /**
     * GitHub OAuth2 authentication endpoint
     */
    public function githubAuth(Request $request)
    {
        return $this->handleSocialAuth('github', $request);
    }

    /**
     * Generic social authentication handler
     */
    private function handleSocialAuth($provider, Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'token' => 'required|string',
                'email' => 'nullable|email',
                'name' => 'nullable|string',
            ]);

            // Verify social media token
            $socialUser = $this->verifySocialToken($provider, $validated['token']);

            if (! $socialUser) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid {$provider} token",
                    'errors' => ['token' => "Failed to verify {$provider} token"],
                ], 401);
            }

            // Extract user data from social provider
            $userData = $this->extractUserData($provider, $socialUser, $validated);

            // Find or create user
            $user = $this->findOrCreateUser($userData, $provider);

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create or update user account',
                ], 500);
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Social authentication successful',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'username' => $user->username,
                    ],
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify social media token with provider
     */
    private function verifySocialToken($provider, $token)
    {
        try {
            switch ($provider) {
                case 'google':
                    return $this->verifyGoogleToken($token);
                case 'facebook':
                    return $this->verifyFacebookToken($token);
                case 'github':
                    return $this->verifyGithubToken($token);
                default:
                    return null;
            }
        } catch (Exception $e) {
            \Log::error("Error verifying {$provider} token: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Verify Google OAuth2 token
     */
    private function verifyGoogleToken($token)
    {
        try {
            $response = Http::get('https://www.googleapis.com/oauth2/v3/tokeninfo', [
                'access_token' => $token,
            ]);

            if ($response->successful() && ! $response->json('error')) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Verify Facebook OAuth2 token
     */
    private function verifyFacebookToken($token)
    {
        try {
            $response = Http::get('https://graph.facebook.com/v18.0/me', [
                'access_token' => $token,
                'fields' => 'id,email,name,picture',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Verify GitHub OAuth2 token
     */
    private function verifyGithubToken($token)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/vnd.github+json',
            ])->get('https://api.github.com/user');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Extract user data from social provider response
     */
    private function extractUserData($provider, $socialUser, $validated)
    {
        $data = [
            'provider' => $provider,
            'provider_id' => null,
            'email' => null,
            'name' => null,
            'username' => null,
        ];

        switch ($provider) {
            case 'google':
                $data['provider_id'] = $socialUser['user_id'] ?? null;
                $data['email'] = $socialUser['email'] ?? $validated['email'] ?? null;
                $data['name'] = $validated['name'] ?? null;
                break;

            case 'facebook':
                $data['provider_id'] = $socialUser['id'] ?? null;
                $data['email'] = $socialUser['email'] ?? $validated['email'] ?? null;
                $data['name'] = $socialUser['name'] ?? $validated['name'] ?? null;
                break;

            case 'github':
                $data['provider_id'] = $socialUser['id'] ?? null;
                $data['email'] = $socialUser['email'] ?? $validated['email'] ?? null;
                $data['name'] = $socialUser['name'] ?? $validated['name'] ?? null;
                $data['username'] = $socialUser['login'] ?? null;
                break;
        }

        return $data;
    }

    /**
     * Find or create user account
     */
    private function findOrCreateUser($userData, $provider)
    {
        try {
            // Try to find user by email
            if ($userData['email']) {
                $user = User::where('email', $userData['email'])->first();
                if ($user) {
                    // Update user with social provider info
                    $user->update([
                        'name' => $userData['name'] ?? $user->name,
                        'username' => $userData['username'] ?? $user->username,
                        'is_email_verified' => true,
                        'email_verified_at' => now(),
                    ]);

                    return $user;
                }
            }

            // Create new user
            $username = $this->generateUniqueUsername($userData['username'] ?? $userData['name']);

            $user = User::create([
                'name' => $userData['name'] ?? 'Social User',
                'username' => $username,
                'email' => $userData['email'],
                'password' => bcrypt(bin2hex(random_bytes(32))),
                'is_email_verified' => 1,
                'email_verified_at' => now(),
                'is_active' => 1,
                'type' => 'social_'.$provider,
            ]);

            return $user;
        } catch (Exception $e) {
            \Log::error('Error creating user: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Generate unique username
     */
    private function generateUniqueUsername($baseUsername = null)
    {
        if (! $baseUsername) {
            $baseUsername = 'user';
        }

        $username = str_slug($baseUsername);
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = str_slug($baseUsername).$counter;
            $counter++;
        }

        return $username;
    }
}
