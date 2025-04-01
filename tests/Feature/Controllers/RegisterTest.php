<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful user registration with valid data.
     * @test
     */
    public function user_can_register_with_valid_data(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $userData = [
            'name'                  => 'Test User',
            'email'                 => 'testregister@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'user',
        ];

        $response = $this->postJson('/auth/register', $userData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => [
                         'id',
                         'name',
                         'email',
                     ],
                     'token',
                 ])
                 ->assertJsonFragment(['message' => 'Account registered successfully']);

        $this->assertDatabaseHas('users', [
            'email' => 'testregister@example.com',
            'name'  => 'Test User',
        ]);

        $user = User::where('email', 'testregister@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotEquals('password123', $user->password);
    }

    /**
     * Test registration fails if passwords do not match.
     * @test
     */
    public function registration_fails_if_passwords_do_not_match(): void
    {
        $userData = [
            'name'                  => 'Test User',
            'email'                 => 'testmismatch@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password456',
        ];

        $response = $this->postJson('/auth/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('password');

        $this->assertDatabaseMissing('users', [
            'email' => 'testmismatch@example.com',
        ]);
    }

    /**
     * Test registration fails if email already exists.
     * @test
     */
    public function registration_fails_if_email_already_exists(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name'                  => 'Another User',
            'email'                 => 'existing@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/auth/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }

    /**
     * Test registration fails if required fields are missing.
     * @test
     */
    public function registration_fails_if_required_fields_are_missing(): void
    {
        $userData = [
            'email'                 => 'missingfields@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/auth/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('name');
    }
}
