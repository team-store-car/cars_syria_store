<?php
namespace Database\Factories;

use App\Models\Workshop;
use App\Models\WorkshopAd;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkshopAdFactory extends Factory
{
    protected $model = WorkshopAd::class;

    public function definition()
    {
        return [
            'workshop_id' => Workshop::factory(),
            'title'       => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'price'       => $this->faker->randomFloat(2, 100, 5000),
        ];
    }
}
