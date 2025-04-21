<?php
namespace App\Services;

use App\Interfaces\AiRecommendationInterface;
// ازالة: use App\Interfaces\CarRepositoryInterface;
use Illuminate\Support\Collection; // أو array مباشرة
use Illuminate\Support\Facades\Log;

class CarRecommendationService
{
    public function __construct(
        private readonly AiRecommendationInterface $aiService
    ) {}

    /**
     * @throws \Exception
     */
    public function generateRecommendations(array $userAnswers): array // تغيير نوع الإرجاع إذا لزم الأمر
    {
        Log::info('Generating car recommendations based on answers.', ['answers_count' => count($userAnswers)]);

        // 1. الحصول على قائمة السيارات المقترحة مباشرة من خدمة الذكاء الاصطناعي
        $recommendations = $this->aiService->getSuggestions($userAnswers);
        Log::info('AI generated recommendations.', ['recommendations' => $recommendations]);

        // لا حاجة لاستدعاء CarRepository

        // يمكنك إضافة تنسيق بسيط هنا إذا لزم الأمر قبل الإرجاع
        // مثلاً، التأكد من أنها مصفوفة، تحديد عدد النتائج، إلخ.
        // $recommendations = array_slice($recommendations, 0, config('ai.recommendation_limit', 5));

        return $recommendations; // إرجاع النتيجة مباشرة
    }
}
