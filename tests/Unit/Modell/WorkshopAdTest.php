<?php

namespace Tests\Unit\Modell;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopAd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class WorkshopAdTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_can_create_a_workshop_ad()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $user = User::factory()->withRole('workshop')->create();

        $workshop = Workshop::factory()->create(['user_id' => $user->id]);
        

        $this->actingAs($user, 'sanctum');
        $data = [
            'workshop_id' => $workshop->id,
            'title'       => 'Oil Change Service',
            'description' => 'Professional oil change for your car.',
            'price'       => 200.00,
        ];

        $response = $this->postJson('/api/workshop-ads', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('workshop_ads', [
            'title'       => 'Oil Change Service',
            'description' => 'Professional oil change for your car.',
            'price'       => 200.00,
        ]);
    }
}
