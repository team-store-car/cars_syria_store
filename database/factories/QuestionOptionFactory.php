<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionOptionFactory extends Factory
{
    protected $model = QuestionOption::class;

    public function definition()
    {
        return [
            'question_id' => Question::factory(), // يرتبط تلقائيًا ب Question جديد
            'text' => $this->faker->word(), // نص عشوائي للخيار
            // 'value' => $this->faker->randomNumber(),
            // 'order' => $this->faker->numberBetween(1, 5),
            // 'is_active' => $this->faker->boolean(90),
        ];
    }
}