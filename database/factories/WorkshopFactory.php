<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkshopFactory extends Factory
{
    protected $model = Workshop::class;

    public function definition()
    {
        return [
            'user_id'                          => User::factory(),
            'name'                             => 'Workshop ' . $this->faker->companySuffix(),
            'location'                         => 'Street ' . $this->faker->buildingNumber(),
            'city'                             => $this->faker->randomElement(['Aleppo', 'Damascus', 'Homs']),
            'description'                      => $this->faker->sentence(6, true),
            'commercial_registration_number'   => 'CR' . $this->faker->unique()->numerify('######'),
            'commercial_registration_image'    => $this->faker->imageUrl(640, 480, 'business', true),
            'certification_details'            => $this->faker->randomElement(['ISO certified', 'Government Approved', 'Top Rated']),
            'verified'                         => $this->faker->boolean(70), // 70% chance true
        ];
    }
}
