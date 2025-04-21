<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'category_id' => Category::factory(),
            'country_of_manufacture' => $this->faker->country(),
            'model' => $this->faker->word(),
            'year' => $this->faker->year(),
            'condition' => $this->faker->randomElement(['new', 'used']),
            'mileage' => $this->faker->optional()->numberBetween(1000, 200000),
            'fuel_type' => $this->faker->randomElement(['Petrol', 'Diesel', 'Electric', 'Hybrid']),
            'transmission' => $this->faker->randomElement(['Automatic', 'Manual']),
            'horsepower' => $this->faker->optional()->numberBetween(50, 500),
            'seats' => $this->faker->numberBetween(2, 7),
            'color' => $this->faker->safeColorName(),
            'description' => $this->faker->optional()->paragraph(),
            'is_featured' => $this->faker->boolean(20),
            'other_benefits' => $this->faker->optional()->sentence(),
        ];
    }
}