<?php

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_create_user_instance()
    {
        $user = new User([
            'name' => 'Ahmed',
            'email' => 'ahmad@example.com',
            'password' => 'wsx123',
        ]);

        $this->assertEquals('Ahmed', $user->name);
        $this->assertEquals('ahmad@example.com', $user->email);
        $this->assertEquals('wsx123', $user->password);
    }
}
