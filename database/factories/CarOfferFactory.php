<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarOffer>
 */
class CarOfferFactory extends Factory
{
    protected $model = CarOffer::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $offerType = $this->faker->randomElement(['sale', 'rent']);

        return [
            'car_id' => Car::factory(),
            'offer_type' => $offerType,
            'price' => $this->faker->randomFloat(2, 1000, 500000), // السعر بين 1000 و500,000
            'price_unit' => 'SYR',
            'location' => $this->faker->city, // موقع مثل "دمشق" أو "حلب"
            'pricing_period' => $offerType === 'rent' ? $this->faker->randomElement(['daily', 'weekly', 'monthly']) : null,
            'is_available' => true, // 90% احتمال أن يكون متاحًا
            'additional_features' => $this->faker->randomElement([
                'GPS Navigation System', 'Comprehensive Insurance', 'Rear Camera', 'Leather Seats',
                null,
            ]),
        ];
    }
}
