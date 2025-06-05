<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function redirectToProvider()
    {
        try {
            Log::info('Starting Google OAuth redirect');
            
            return Socialite::driver('google')
                ->with([
                    'prompt' => 'select_account',
                    'access_type' => 'offline'
                ])
                ->stateless()
                ->redirect();
            
        } catch (Exception $e) {
            Log::error('Google Auth Redirect Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Fehler bei der Weiterleitung zu Google: ' . $e->getMessage()
            ], 400);
        }
    }

    public function handleProviderCallback(Request $request)
    {
        try {
            if ($request->has('error')) {
                throw new Exception('Google OAuth Error: ' . $request->get('error'));
            }

            $googleUser = Socialite::driver('google')->stateless()->user();
            
            Log::info('Successfully retrieved Google user', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName()
            ]);
            
            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->getEmail()
                ],
                [
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(Str::random(16)),
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'role' => 'user'
                ]
            );

            // Manuelles Login
            Auth::login($user);
            
            // Token generieren
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Erfolgreich angemeldet',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ])->withCookie(cookie('auth_token', $token, 60 * 24));

        } catch (Exception $e) {
            Log::error('Google Auth Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Anmeldung fehlgeschlagen: ' . $e->getMessage()
            ], 500);
        }
    }
} 