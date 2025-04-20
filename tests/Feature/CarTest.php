<?php

namespace Tests\Feature;

use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_cars()
    {
        Car::factory()->count(3)->create();
        $response = $this->getJson('/api/cars');
        $response->assertStatus(200)->assertJsonCount(3);
    }

    public function test_can_create_car()
    {
        $carData = Car::factory()->make()->toArray();
        $response = $this->postJson('/api/cars', $carData);
        $response->assertStatus(201)->assertJsonFragment(['name' => $carData['name']]);
    }

    public function test_validation_fails_when_creating_car()
    {
        $response = $this->postJson('/api/cars', []);
        $response->assertStatus(422)->assertJsonStructure(['errors']);
    }

    public function test_can_update_car()
    {
        $car = Car::factory()->create();
        $response = $this->putJson("/api/cars/{$car->id}", ['name' => 'Updated Car']);
        $response->assertStatus(200)->assertJsonFragment(['name' => 'Updated Car']);
    }

    public function test_can_delete_car()
    {
        $car = Car::factory()->create();
        $response = $this->deleteJson("/api/cars/{$car->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }
}
