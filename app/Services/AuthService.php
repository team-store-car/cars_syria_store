<?php
namespace App\Services;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {

        $user = $this->userRepository->createUser($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        if (isset($data['role'])) {
            $this->userRepository->assignRole($user, $data['role']);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        \Log::info("Generated token: " . $token);

        return [
            'user' => $user,
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
            'user' => $user,
            'token' => $token
        ];
    }
}
