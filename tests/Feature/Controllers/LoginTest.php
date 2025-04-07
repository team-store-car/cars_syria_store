<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user',
                'token'
            ]);
    }

    // Test that a user cannot log in with invalid credentials
    public function test_user_cannot_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
        ->assertJson(['message' => 'Invalid login credentials']);

    }

    // Test that email and password are required for login
    public function test_login_requires_email_and_password()
    {
        $response = $this->postJson('/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
