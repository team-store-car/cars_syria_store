<?php 
namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthService
{
    public function __construct(private UserRepository $userRepository) {}

    public function register(array $data): array
    {
        $user = $this->userRepository->create($data);

        // تعيين الدور للمستخدم
        $role = Role::where('name', $data['role'] ?? 'user')->first();
        if ($role) {
            $user->assignRole($role);
        }

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }
}
