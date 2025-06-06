<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $loginResult = $this->authService->login($credentials['email'], $credentials['password']);

        if (!$loginResult) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user'    => $loginResult['user'],
            'token'   => $loginResult['token']
        ]);
    }
}