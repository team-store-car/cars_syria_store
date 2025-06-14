<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class EnsureEmailIsVerifiedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Test-Route erstellen, die die verified Middleware verwendet
        Route::get('/test-verified', function () {
            return response()->json(['message' => 'Access granted']);
        })->middleware(['auth:sanctum', 'verified']);
    }

    public function test_unauthenticated_user_cannot_access_verified_route()
    {
        $response = $this->getJson('/test-verified');

        $response->assertStatus(401);
    }

    public function test_unverified_user_cannot_access_verified_route()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/test-verified');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Your email address is not verified.',
                'status' => 'email_not_verified'
            ]);
    }

    public function test_verified_user_can_access_verified_route()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/test-verified');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Access granted'
            ]);
    }
}