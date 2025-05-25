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
use App\Repositories\UserRepository;

// This controller handles the registration of new users.
class RegisterController extends Controller
{
    public function __construct(AuthService $authService, UserRepository $userRepository)
    {
        $this->authService = $authService;
        $this->userRepository = $userRepository;
    } 
    public function register(RegisterRequest $request): JsonResponse
    {
        $userData = $request->validated();
        \Log::info('Starting registration process with data:', ['email' => $userData['email']]);

        $registrationResult = $this->authService->register($userData);
        
        \Log::info('Registration completed', [
            'user_id' => $registrationResult['user']->id,
            'token_length' => strlen($registrationResult['token'])
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Account successfully registered',
            'data' => [
                'user' => $registrationResult['user'],
                'token' => $registrationResult['token'],
                'token_type' => 'Bearer'
            ]
        ], 201);
    }
}