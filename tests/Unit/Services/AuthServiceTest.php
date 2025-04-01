<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    public function test_login_returns_user_and_token_on_success()
    {
        // إنشاء مستخدم وهمي
        $user = Mockery::mock(User::class);
        $user->allows('setAttribute');
        $user->allows('getAttribute');
        $user->email = 'test@example.com';
        $user->password = Hash::make('password123');

        // توقعات لدالة createToken
        $tokenResult = new class {
            public $plainTextToken = 'test_token';
        };
        $user->shouldReceive('createToken')->once()->andReturn($tokenResult);

        Auth::shouldReceive('attempt')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        $userRepository = Mockery::mock(UserRepository::class);
        $authService = new AuthService($userRepository);

        $result = $authService->login('test@example.com', 'password123');

        $this->assertNotNull($result);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertEquals('test_token', $result['token']);
    }

    public function test_login_returns_null_on_failure()
    {
        Auth::shouldReceive('attempt')->once()->andReturn(false);
        Auth::shouldReceive('user')->never();

        $userRepository = Mockery::mock(UserRepository::class);
        $authService = new AuthService($userRepository);

        $result = $authService->login('test@example.com', 'wrongpassword');

        $this->assertNull($result);
    }
}