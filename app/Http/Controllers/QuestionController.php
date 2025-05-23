<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\QuestionRepository;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    protected $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * إرجاع كل الأسئلة مع الخيارات على شكل JSON.
     */
    public function index(): JsonResponse
    {
        $questions = $this->questionRepository->getAllWithOptions();

        return response()->json([
            'status' => true,
            'data' => $questions
        ]);
    }
}
