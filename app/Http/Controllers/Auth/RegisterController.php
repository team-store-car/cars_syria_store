<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\JsonResponse;
use App\Repositories\AuthRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Access\AuthorizationException;

// This controller handles the registration of new users.
class RegisterController extends Controller
{
    public function __construct(AuthService $authService, AuthRepository $userRepository)
    {
        $this->authService = $authService;
        $this->userRepository = $userRepository;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $userData = $request->validated();
        \Log::info('Starting registration process with data:', ['email' => $userData['email']]);

        $registrationResult = $this->authService->register($userData);

        // Trigger verification email
        // event(new Registered($registrationResult['user']));

        \Log::info('Registration completed', [
            'user_id' => $registrationResult['user']->id,
            'token_length' => strlen($registrationResult['token'])
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Account successfully registered. Please check your email for verification link.',
            'data' => [
                'user' => $registrationResult['user'],
                'token' => $registrationResult['token'],
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    public function verify($id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent'], 200);
    }
}
