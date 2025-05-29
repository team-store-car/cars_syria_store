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

        $registrationResult = $this->authService->register($userData);

        return response()->json([
            'message' => 'Account successfully registered',
            'user'    => $registrationResult['user'],
            'token'   => $registrationResult['token'],
        ], 201);
    }
}