<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopAd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class WorkshopAdControllerTest extends TestCase
{
    use RefreshDatabase;

#[Test]
    public function test_allows_authenticated_workshop_owners_to_create_ads()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $user = User::factory()->withRole('workshop')->create();
        $workshop = Workshop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');


        $adData = [
            'title' => 'Test Ad Create',
            'description' => 'Test Description',
            'price' => 100,
        ];

        $response = $this->postJson('/api/workshop-ads', $adData);

        $response->assertCreated()
                 ->assertJsonStructure(['id', 'workshop_id', 'title', 'description', 'price', 'created_at', 'updated_at']);

        $this->assertDatabaseHas('workshop_ads', [
            'workshop_id' => $workshop->id,
            'title' => 'Test Ad Create'
        ]);
    }

    #[Test]
    public function test_prevents_users_without_workshops_from_creating_ads()
    {
        $userWithoutWorkshop = User::factory()->withRole('workshop')->create();
        $this->actingAs($userWithoutWorkshop, 'sanctum');


        $adData = ['title' => 'Test Ad', 'description' => 'Desc', 'price' => 100];

        $response = $this->postJson('/api/workshop-ads', $adData);

        $response->assertForbidden()
                 ->assertJson(['message' => 'يجب أن تكون مالك ورشة لإنشاء إعلان']);

        $this->assertDatabaseMissing('workshop_ads', ['title' => 'Test Ad']);
    }

    #[Test]
    public function test_validates_workshop_ad_data_on_create()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $user = User::factory()->withRole('workshop')->create();
        Workshop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');


        $response = $this->postJson('/api/workshop-ads', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'description', 'price']);
    }

    #[Test]
    public function test_allows_workshop_owner_to_update_their_ad()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $user = User::factory()->withRole('workshop')->create();
        $workshop = Workshop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');


        $workshopAd = WorkshopAd::factory()->create(['workshop_id' => $workshop->id]);
        $updateData = ['title' => 'Updated Title', 'price' => 150.50];

        $response = $this->putJson('/api/workshop-ads/' . $workshopAd->id, $updateData);

        $response->assertOk()
                 ->assertJson([
                     'id' => $workshopAd->id,
                     'title' => 'Updated Title',
                     'price' => 150.50,
                 ]);

        $this->assertDatabaseHas('workshop_ads', [
            'id' => $workshopAd->id,
            'title' => 'Updated Title',
            'price' => 150.50,
        ]);
    }

    #[Test]
    public function test_prevents_updating_ad_owned_by_another_workshop()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $mainUser = User::factory()->withRole('workshop')->create();
        Workshop::factory()->create(['user_id' => $mainUser->id]);
        $this->actingAs($mainUser, 'sanctum');

        $otherUser = User::factory()->withRole('workshop')->create();
        $otherWorkshop = Workshop::factory()->create(['user_id' => $otherUser->id]);
        $workshopAd = WorkshopAd::factory()->create(['workshop_id' => $otherWorkshop->id]);

        $response = $this->putJson('/api/workshop-ads/' . $workshopAd->id, ['title' => 'Trying to Update']);

        $response->assertForbidden();

        $this->assertDatabaseHas('workshop_ads', [
            'id' => $workshopAd->id,
            'title' => $workshopAd->title,
        ]);

        $this->assertDatabaseMissing('workshop_ads', [
            'id' => $workshopAd->id,
            'title' => 'Trying to Update',
        ]);
    }

    #[Test]
    public function test_returns_not_found_when_updating_non_existent_ad()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $user = User::factory()->withRole('workshop')->create();
        Workshop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');


        $updateData = ['title' => 'Updated Title'];
        $response = $this->putJson('/api/workshop-ads/999', $updateData);

        $response->assertNotFound();
    }

    #[Test]
    public function test_validates_workshop_ad_data_on_update()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $user = User::factory()->withRole('workshop')->create();
        $workshop = Workshop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');


        $workshopAd = WorkshopAd::factory()->create(['workshop_id' => $workshop->id]);
        $invalidData = ['title' => '', 'price' => -50];

        $response = $this->putJson('/api/workshop-ads/' . $workshopAd->id, $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'price']);

        $this->assertDatabaseHas('workshop_ads', [
            'id' => $workshopAd->id,
            'title' => $workshopAd->title,
            'price' => $workshopAd->price,
        ]);
    }

    #[Test]
    public function test_allows_workshop_owner_to_delete_their_ad()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $user = User::factory()->withRole('workshop')->create();
        $workshop = Workshop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');


        $workshopAd = WorkshopAd::factory()->create(['workshop_id' => $workshop->id]);
        $adId = $workshopAd->id;

        $response = $this->deleteJson('/api/workshop-ads/' . $adId);

        $response->assertNoContent();
        $this->assertDatabaseMissing('workshop_ads', ['id' => $adId]);
    }

    #[Test]
    public function test_prevents_deleting_ad_owned_by_another_workshop()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $mainUser = User::factory()->withRole('workshop')->create();
        Workshop::factory()->create(['user_id' => $mainUser->id]);
        $this->actingAs($mainUser, 'sanctum');

        $otherUser = User::factory()->create(['role' => 'workshop']);
        $otherWorkshop = Workshop::factory()->create(['user_id' => $otherUser->id]);
        $workshopAd = WorkshopAd::factory()->create(['workshop_id' => $otherWorkshop->id]);

        $response = $this->deleteJson('/api/workshop-ads/' . $workshopAd->id);

        $response->assertForbidden();
        $this->assertDatabaseHas('workshop_ads', ['id' => $workshopAd->id]);
    }

    #[Test]
    public function test_returns_not_found_when_deleting_non_existent_ad()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $user = User::factory()->withRole('workshop')->create();
        Workshop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');


        $response = $this->deleteJson('/api/workshop-ads/999');

        $response->assertNotFound();
    }
}
