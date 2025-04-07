<?php
namespace Database\Factories;

use App\Models\WorkshopAd;
use App\Models\WorkshopAdImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkshopAdImageFactory extends Factory
{
    protected $model = WorkshopAdImage::class;

    public function definition()
    {
        return [
            'workshop_ad_id' => WorkshopAd::factory(),
            'image_path'     => $this->faker->imageUrl(),
        ];
    }
}
