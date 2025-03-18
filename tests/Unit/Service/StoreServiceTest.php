<?php

namespace Tests\Unit\Services;

use App\Contracts\FileStorageInterface;
use App\Models\Store;
use App\Models\User;
use App\Repositories\StoreRepository;
use App\Services\StoreService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreServiceTest extends TestCase
{
    protected $storeRepositoryMock;
    protected $fileStorageServiceMock;
    protected $storeService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks for StoreRepository and FileStorageInterface
        $this->storeRepositoryMock = Mockery::mock(StoreRepository::class);
        $this->fileStorageServiceMock = Mockery::mock(FileStorageInterface::class);

        // Initialize StoreService with the mocks
        $this->storeService = new StoreService($this->storeRepositoryMock, $this->fileStorageServiceMock);
    }

    #[Test]
    public function it_can_create_a_store()
    {
        // Mock the data to be sent
        $user = User::factory()->make(); // make() creates an instance without saving to DB
        $data = [
            'name' => 'Test Store',
            'user_id' => $user->id,
            'description' => 'Test description',
            'address' => 'Test address',
            'phone' => '123456789',
            'email' => 'test@example.com',
            'website' => 'http://test.com',
            'status' => 'active'
        ];

        // Mock the 'create' method
        $storeMock = Mockery::mock(Store::class);
        $this->storeRepositoryMock->shouldReceive('create')
                                  ->once()
                                  ->with($data)
                                  ->andReturn($storeMock);

        // Test the service
        $store = $this->storeService->createStore($data);

        // Ensure the returned object is of type Store
        $this->assertInstanceOf(Store::class, $store);
    }

    #[Test]
    public function it_can_get_store_by_id()
    {
        // Mock the findById method
        $storeMock = Mockery::mock(Store::class);
        $this->storeRepositoryMock->shouldReceive('findById')
                                  ->once()
                                  ->with(1)
                                  ->andReturn($storeMock);

        // Test the service
        $store = $this->storeService->getStoreById(1);

        // Ensure the returned object is of type Store
        $this->assertInstanceOf(Store::class, $store);
    }

    #[Test]
    public function it_returns_null_when_store_not_found()
    {
        // Mock the findById method
        $this->storeRepositoryMock->shouldReceive('findById')
                                  ->once()
                                  ->with(9999)
                                  ->andReturn(null);

        // Test the service
        $store = $this->storeService->getStoreById(9999);

        // Ensure the result is null
        $this->assertNull($store);
    }

    #[Test]
    public function test_it_can_update_a_store()
    {
        // Create a mock store
        $store = Store::factory()->make(['id' => 1, 'name' => 'Old Store', 'logo' => 'old_logo.png']);

        // Update data
        $updatedData = ['name' => 'Updated Store'];

        // Updated store mock
        $updatedStore = clone $store;
        $updatedStore->name = 'Updated Store';

        // Expect `findById` to be called
        $this->storeRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($store->id)
            ->andReturn($store);

        // Expect `update` to be called and return updated store
        $this->storeRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($store->id, $updatedData)
            ->andReturn($updatedStore);

        // Call `updateStore`
        $result = $this->storeService->updateStore($store->id, $updatedData);

        // Ensure the name was updated
        $this->assertEquals('Updated Store', $result->name);
    }

    // #[Test]
    // public function testCanUpdateStoreWithNewLogo()
    // {
    //     // Create a fake file
    //     $file = UploadedFile::fake()->image('new-logo.jpg');

    //     // Update data
    //     $data = [
    //         'logo' => $file,
    //     ];

    //     // Create a partial mock store
    //     $store = Mockery::mock(Store::class)->makePartial();
    //     $store->id = 1;
    //     $store->logo = 'old-logo.jpg';

    //     // Mock method calls
    //     $this->storeRepositoryMock
    //         ->shouldReceive('findById')
    //         ->once()
    //         ->with(1)
    //         ->andReturn($store);

    //     $this->fileStorageServiceMock
    //         ->shouldReceive('delete')
    //         ->once()
    //         ->with('old-logo.jpg');

    //     $this->fileStorageServiceMock
    //         ->shouldReceive('upload')
    //         ->once()
    //         ->with($file, 'stores/logos')
    //         ->andReturn('stores/logos/new-logo.jpg');

    //     $this->storeRepositoryMock
    //         ->shouldReceive('update')
    //         ->once()
    //         ->with(1, ['logo' => 'stores/logos/new-logo.jpg'])
    //         ->andReturn($store);

    //     // Execute the test
    //     $updatedStore = $this->storeService->updateStore(1, $data);

    //     // Ensure the returned object is of type Store
    //     $this->assertInstanceOf(Store::class, $updatedStore);

    //     // Expected file path without timestamp
    //     $expectedFilePath = 'stores/logos/new-logo.jpg';

    //     // Actual file path after storage
    //     $actualFilePath = $updatedStore->logo;

    //     // Remove timestamp from actual file path
    //     $actualFilePathWithoutTime = preg_replace('/\/\d+_/', '/', $actualFilePath);

    //     // Ensure the paths match after timestamp removal
    //     $this->assertEquals($expectedFilePath, $actualFilePathWithoutTime);
    // }

    #[Test]
    public function it_can_delete_a_store()
    {
        // Mock the delete method
        $this->storeRepositoryMock->shouldReceive('delete')
                                  ->once()
                                  ->with(1)
                                  ->andReturn(true);

        // Test the service
        $deleted = $this->storeService->deleteStore(1);

        // Ensure deletion was successful
        $this->assertTrue($deleted);
    }

    protected function tearDown(): void
    {
        // Close Mockery after each test
        Mockery::close();
        parent::tearDown();
    }
}
