<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    #[Test]
    public function it_can_list_all_stores()
    {
        Store::factory(5)->create([
            'user_id'=>$this->user->id,
        ]);

        $response = $this->getJson('/api/dashboard/stores');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    #[Test]
    public function it_can_create_a_new_store()
    {

        $data = [
            'name' => 'New Store',
            'user_id' => $this->user->id,
            'description' => 'A new store',
            'address' => '456 New Street',
            'phone' => '987654321',
            'email' => 'new@store.com',
            'status' => 'active'
        ];

        $response = $this->postJson('/api/dashboard/stores', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'New Store']);
    }

    #[Test]
    public function it_can_show_a_single_store()
    {
        $store = Store::factory()->create([
            'user_id'=>$this->user->id,
            ]);

        $response = $this->getJson("/api/dashboard/stores/{$store->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $store->name]);
    }

    #[Test]
    public function it_can_update_a_store()
    {
        $store = Store::factory()->create([
            'user_id'=>$this->user->id,
        ]);

        $data = ['name' => 'Updated Store'];

        $response = $this->putJson("/api/dashboard/stores/{$store->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Store']);
    }

    #[Test]
    public function it_can_delete_a_store()
    {
        $store = Store::factory()->create([
            'user_id'=>$this->user->id
        ]);

        $response = $this->deleteJson("/api/dashboard/stores/{$store->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
    }

    #[Test]
    public function it_validates_store_creation()
    {
        $response = $this->postJson('/api/dashboard/stores', [
            'name' => '',
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    #[Test]
    public function it_returns_404_when_store_not_found()
    {
        $response = $this->getJson(route('stores.show', 9999));

        $response->assertStatus(404);
    }

    #[Test]
    public function it_returns_404_when_updating_non_existing_store()
    {
        $updatedData = ['name' => 'Non Existing Store'];

        $response = $this->putJson(route('stores.update', 9999), $updatedData);

        $response->assertStatus(404);
    }

    #[Test]
    public function it_returns_404_when_deleting_non_existing_store()
    {
        $response = $this->deleteJson(route('stores.destroy', 9999));

        $response->assertStatus(404);
    }

    #[Test]
    public function it_fails_when_creating_store_without_valid_user()
    {
        $storeData = [
            'name' => 'Invalid Store',
            'user_id' => 9999, // Non-existent user
            'description' => 'Test description'
        ];

        $response = $this->postJson(route('stores.store'), $storeData);

        $response->assertStatus(422);
    }

    #[Test]
    public function it_fails_when_updating_store_with_invalid_data()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);

        $invalidData = [
            'name' => '', // Empty store name
            'email' => 'not-an-email'
        ];

        $response = $this->putJson(route('stores.update', $store->id), $invalidData);

        $response->assertStatus(422);
    }

    // This test is applicable after authentication
    // #[Test]
    // public function it_forbids_updating_store_if_not_owner()
    // {
    //     $user1 = User::factory()->create();
    //     $user2 = User::factory()->create();
    //     $store = Store::factory()->create(['user_id' => $user1->id]);

    //     $updatedData = ['name' => 'Unauthorized Update'];

    //     $response = $this->actingAs($user2)->putJson(route('stores.update', $store->id), $updatedData);

    //     $response->assertStatus(403);
    // }

    // #[Test]
    // public function it_forbids_deleting_store_if_not_owner()
    // {
    //     $user1 = User::factory()->create();
    //     $user2 = User::factory()->create();
    //     $store = Store::factory()->create(['user_id' => $user1->id]);

    //     $response = $this->actingAs($user2)->deleteJson(route('stores.destroy', $store->id));

    //     $response->assertStatus(403);
    // }
}