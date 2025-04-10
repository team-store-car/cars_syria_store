<?php
namespace Database\Factories;

use App\Models\Shop;
use App\Models\ShopAd;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopAdFactory extends Factory
{
    protected $model = ShopAd::class;

    public function definition()
    {
        return [
            'shop_id'     => Shop::factory(),
            'title'       => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'price'       => $this->faker->randomFloat(2, 100, 5000),
        ];
    }
}
