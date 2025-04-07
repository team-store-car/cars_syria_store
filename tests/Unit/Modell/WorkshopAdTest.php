<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopAd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopAdTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_workshop_ad()
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $data = [
            'workshop_id' => $workshop->id,
            'title'       => 'Oil Change Service',
            'description' => 'Professional oil change for your car.',
            'price'       => 200.00,
        ];

        $response = $this->postJson(route('workshop-ads.store'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('workshop_ads', [
            'title'       => 'Oil Change Service',
            'description' => 'Professional oil change for your car.',
            'price'       => 200.00,
        ]);
    }
}
