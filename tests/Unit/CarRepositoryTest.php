<?php

namespace Tests\Unit;

use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CarRepository $carRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carRepository = new CarRepository();
    }

    public function test_can_create_car()
    {
        $carData = Car::factory()->make()->toArray();
        $car = $this->carRepository->create($carData);

        $this->assertDatabaseHas('cars', ['name' => $car->name]);
    }

    public function test_can_find_car_by_id()
    {
        $car = Car::factory()->create();
        $foundCar = $this->carRepository->findById($car->id);

        $this->assertNotNull($foundCar);
        $this->assertEquals($car->id, $foundCar->id);
    }

    public function test_returns_null_if_car_not_found()
    {
        $foundCar = $this->carRepository->findById(99999);
        $this->assertNull($foundCar);
    }

    public function test_can_update_car()
    {
        $car = Car::factory()->create();
        $updatedData = ['name' => 'Updated Car Name'];
        $this->carRepository->update($car, $updatedData);

        $this->assertDatabaseHas('cars', ['name' => 'Updated Car Name']);
    }

    public function test_can_delete_car()
    {
        $car = Car::factory()->create();
        $this->carRepository->delete($car->id);

        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }
}