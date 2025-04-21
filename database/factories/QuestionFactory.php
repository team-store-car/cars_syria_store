<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'text' => $this->faker->sentence(), // نص عشوائي للسؤال
            // 'identifier' => $this->faker->unique()->slug(),
            // 'order' => $this->faker->numberBetween(1, 10),
            // 'is_active' => $this->faker->boolean(90), // 90% احتمال true
        ];
    }
}