<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Rollen erstellen, falls sie noch nicht existieren
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
        if (!Role::where('name', 'user')->exists()) {
            Role::create(['name' => 'user']);
        }
    }

    public function test_admin_can_get_all_users()
    {
        // Admin erstellen
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');
        
        // Einige normale Benutzer erstellen
        User::factory()->count(3)->create();

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_non_admin_cannot_get_all_users()
    {
        // Normalen Benutzer erstellen
        $user = User::factory()->create(['role' => 'user']);
        $user->assignRole('user');

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_get_all_users()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }
} 