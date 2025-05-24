<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarFilterApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_brand()
    {
        Car::factory()->create(['brand' => 'BMW', 'name' => 'BMW X5']);
        Car::factory()->create(['brand' => 'Toyota', 'name' => 'Toyota Camry']);

        $response = $this->getJson('/api/cars?brand=BMW');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['brand' => 'BMW', 'name' => 'BMW X5']);
    }

    public function test_filter_by_year()
    {
        Car::factory()->create(['year' => 2020, 'name' => 'BMW X5']);
        Car::factory()->create(['year' => 2018, 'name' => 'Toyota Camry']);

        $response = $this->getJson('/api/cars?year=2020');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['year' => 2020, 'name' => 'BMW X5']);
    }

    public function test_filter_by_mileage_range()
    {
        Car::factory()->create(['mileage' => 20000, 'name' => 'BMW X5']);
        Car::factory()->create(['mileage' => 60000, 'name' => 'Toyota Camry']);

        $response = $this->getJson('/api/cars?mileage[min]=10000&mileage[max]=30000');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['mileage' => 20000, 'name' => 'BMW X5']);
    }

    public function test_filter_by_multiple_criteria()
    {
        Car::factory()->create([
            'brand' => 'BMW',
            'year' => 2020,
            'condition' => 'used',
            'fuel_type' => 'Petrol',
            'seats' => 5,
            'is_featured' => 0,
            'name' => 'BMW X5',
            'is_featured' => true,
        ]);
        Car::factory()->create([
        'brand' => 'Toyota',
         'name' => 'Toyota Camry',
         'year' => 2020,
         'condition' => 'used',
         'fuel_type' => 'Petrol',
         'seats' => 5,
         'is_featured' => true,
        ]);

        $response = $this->getJson('/api/cars?brand=BMW&year=2020&condition=used&fuel_type=Petrol&seats=5&is_featured=true');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'brand' => 'BMW',
                'year' => 2020,
                'condition' => 'used',
                'fuel_type' => 'Petrol',
                'seats' => 5,
                'is_featured' => true,
                'name' => 'BMW X5'
            ]);
    }

    public function test_filter_with_invalid_year()
    {
        $response = $this->getJson('/api/cars?year=1800');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['year']);
    }

    public function test_filter_with_invalid_condition()
    {
        $response = $this->getJson('/api/cars?condition=invalid');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['condition']);
    }

    public function test_filter_with_no_results()
    {
        Car::factory()->create(['brand' => 'BMW', 'name' => 'BMW X5']);

        $response = $this->getJson('/api/cars?brand=NonExistent');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    public function test_filter_by_category_id()
    {
        $category = Category::factory()->create(['name' => 'SUV']);
        Car::factory()->create(['category_id' => $category->id, 'name' => 'BMW X5']);
        Car::factory()->create(['category_id' => Category::factory()->create()->id, 'name' => 'Toyota Camry']);

        $response = $this->getJson("/api/cars?category_id={$category->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'BMW X5']);
    }

    public function test_filter_by_horsepower_range()
    {
        Car::factory()->create(['horsepower' => 200, 'name' => 'BMW X5']);
        Car::factory()->create(['horsepower' => 400, 'name' => 'Tesla Model S']);

        $response = $this->getJson('/api/cars?horsepower[min]=150&horsepower[max]=250');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['horsepower' => 200, 'name' => 'BMW X5']);
    }
}