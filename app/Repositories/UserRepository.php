<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function createUser(array $userData): User
    {
        return User::create([
            'name'     => $userData['name'],
            'email'    => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);
    }

    public function assignRole(User $user, string $role): void
    {
        $user->assignRole($role);
    }
}