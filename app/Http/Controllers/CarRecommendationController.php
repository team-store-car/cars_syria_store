<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetCarRecommendationRequest; // تأكد من المسار
use App\Services\CarRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Log\Logger; // لاستخدام اللوج للتبع أو الأخطاء
use Throwable; // للتعامل مع الأخطاء العامة

class CarRecommendationController extends Controller
{
    public function __construct(
        private readonly CarRecommendationService $recommendationService,
        private readonly Logger $logger // حقن Logger لتسجيل الأخطاء
        ) {}

    /**
     * الحصول على توصيات سيارات بناءً على إجابات المستخدم.
     */
    public function getRecommendations(GetCarRecommendationRequest $request): JsonResponse
    {
        try {
            // استخراج البيانات المتحقق منها من الـ Form Request
            $validatedAnswers = $request->validated()['answers']; // نفترض أن الإجابات تأتي تحت مفتاح 'answers'

            $recommendations = $this->recommendationService->generateRecommendations($validatedAnswers);

            return response()->json([
                'message' => 'Car recommendations generated successfully.',
                'data' => $recommendations,
            ]);

        } catch (Throwable $e) {
            // تسجيل الخطأ للمساعدة في التصحيح
            $this->logger->error('Car recommendation generation failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // كن حذراً عند تسجيل التتبع الكامل في بيئة الإنتاج
                'request_data' => $request->validated() // لا تسجل بيانات حساسة
            ]);

            // إرجاع استجابة خطأ عامة للمستخدم
            return response()->json([
                'message' => 'Failed to generate car recommendations. Please try again later.',
                // 'error' => $e->getMessage() // اختياري: إظهار الخطأ في بيئة التطوير فقط
            ], 500); // Internal Server Error
        }
    }
}