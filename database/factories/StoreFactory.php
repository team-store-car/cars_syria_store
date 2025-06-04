<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $file = UploadedFile::fake()->image('logo.png');
        return [
            'name' => $this->faker->company,
            'user_id' => 1,
            'description' => $this->faker->text(),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'website' => $this->faker->url,
            'logo' =>$file,
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
