<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Shop;
use App\Models\User;

class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'description' => $this->faker->sentence,
            'verified' => $this->faker->boolean,
            'commercial_registration_number' => $this->faker->unique()->numerify('CRN#####'),
            'commercial_registration_image' => $this->faker->imageUrl(),
        ];
    }
}
