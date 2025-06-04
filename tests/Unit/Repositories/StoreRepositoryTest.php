<?php

namespace Tests\Unit\Repositories;

use App\Models\Store;
use App\Models\User;
use App\Repositories\StoreRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreRepositoryTest extends TestCase
{
    use RefreshDatabase; // Ensures the database is reset after each test

    protected StoreRepository $storeRepository;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storeRepository = new StoreRepository(new Store()); // Creating a real instance instead of a Mock
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_create_a_store()
    {
        $data = [
            'name' => 'Test Store',
            'user_id' => $this->user->id,
            'description' => 'Test description',
            'address' => 'Test address',
            'phone' => '123456789',
            'email' => 'test@example.com',
            'website' => 'http://test.com',
            'status' => 'active'
        ];

        $store = $this->storeRepository->create($data);

        $this->assertInstanceOf(Store::class, $store);
        $this->assertDatabaseHas('stores', ['name' => 'Test Store']);
    }

    #[Test]
    public function it_can_get_a_store_by_id()
    {
        $store = Store::factory()->create([
            'user_id'=>$this->user->id,
        ]);

        $foundStore = $this->storeRepository->findById($store->id);

        $this->assertInstanceOf(Store::class, $foundStore);
        $this->assertEquals($store->id, $foundStore->id);
    }

    #[Test]
    public function it_can_update_a_store()
    {
        $store = Store::factory()->create([
            'user_id'=>$this->user->id,
        ]);

        $updatedData = ['name' => 'Updated Store'];

        $updatedStore = $this->storeRepository->update($store->id, $updatedData);

        $this->assertInstanceOf(Store::class, $updatedStore);
        $this->assertEquals('Updated Store', $updatedStore->name);
        $this->assertDatabaseHas('stores', ['name' => 'Updated Store']);
    }

    #[Test]
    public function it_can_delete_a_store()
    {
        $store = Store::factory()->create([
            'user_id'=>$this->user->id,
        ]);

        $this->storeRepository->delete($store->id);

        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
    }

    #[Test]
    public function it_returns_null_when_store_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->storeRepository->findById(9999);
    }
}
