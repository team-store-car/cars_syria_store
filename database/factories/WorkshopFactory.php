<?php

namespace Database\Factories;

use App\Models\User; // استيراد نموذج User
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkshopFactory extends Factory
{
    protected $model = Workshop::class;

    public function definition()
    {
        return [
       
            'user_id'     => User::factory(),
            'name'        => $this->faker->company() . ' Workshop',
            'commercial_registration_number' => $this->faker->unique()->numerify('CRN#######'), 
            'location'    => $this->faker->address(),
            'description' => $this->faker->paragraph(2),
            'certification_details' => $this->faker->sentence,
            'verified'    => $this->faker->boolean(),
            'commercial_registration_image' => $this->faker->imageUrl(640, 480, 'business', true), 
        ];
    }
}