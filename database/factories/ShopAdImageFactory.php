<?php
namespace Database\Factories;

use App\Models\ShopAd;
use App\Models\ShopAdImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopAdImageFactory extends Factory
{
    protected $model = ShopAdImage::class;

    public function definition()
    {
        return [
            'shop_ad_id'  => ShopAd::factory(),
            'image_path'  => $this->faker->imageUrl(),
        ];
    }
}
