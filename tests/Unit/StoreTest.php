<?php

namespace Tests\Unit;

use App\Contracts\FileStorageInterface;
use App\Models\Store;
use App\Repositories\StoreRepository;
use App\Services\StoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected $storeService;
    protected $storeRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the Repository using Mock
        $this->storeRepository = Mockery::mock(StoreRepository::class);
        $this->storeService = new StoreService($this->storeRepository, Mockery::mock(FileStorageInterface::class));
    }

    #[Test]
    public function it_can_create_a_store()
    {
        $data = [
            'name' => 'Test Store',
            'user_id' => 1,
            'description' => 'A test store description',
            'address' => '123 Test Street',
            'phone' => '123456789',
            'email' => 'test@store.com',
            'status' => 'active'
        ];

        $this->storeRepository->shouldReceive('create')->once()->with($data)->andReturn((object) $data);

        $store = $this->storeService->createStore($data);

        $this->assertEquals('Test Store', $store->name);
        $this->assertEquals('test@store.com', $store->email);
    }

    #[Test]
    public function it_can_delete_a_store()
    {
        $this->storeRepository->shouldReceive('delete')->once()->with(1)->andReturn(true);

        $result = $this->storeService->deleteStore(1);

        $this->assertTrue($result);
    }
}