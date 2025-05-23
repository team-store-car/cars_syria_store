<?php

namespace App\Repositories;

use App\Models\Question;

class QuestionRepository
{
    /**
     * جلب كل الأسئلة مع الخيارات المرتبطة بها.
     */
    public function getAllWithOptions()
    {
        return Question::with('options')->get();
    }
}
