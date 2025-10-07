<?php
namespace App\Services;

use App\Http\Resources\UserResource;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    private AuthRepository $userRepository;

    public function __construct(AuthRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {

        $user = $this->userRepository->createUser($data);
        $user->assignRole('user');
        $token = $user->createToken('auth_token')->plainTextToken;

        if (isset($data['role'])) {
            $this->userRepository->assignRole($user, $data['role']);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        Log::info("Generated token: " . $token);

        return [
            'user' => new UserResource($user),
            'token' => $token
        ];
    }

    public function login(string $email, string $password): ?array
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return null;
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => new UserResource($user),
            'token' => $token
        ];
    }
}
