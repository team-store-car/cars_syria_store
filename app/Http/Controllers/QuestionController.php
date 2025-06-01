<?php

namespace App\Http\Controllers;

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
        try {
            $questions = $this->questionRepository->getAllWithOptions();

            if (!$questions || $questions->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يوجد اي سؤال'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $questions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في الاتصال بالخادم'
            ], 500);
        }
    }
}
