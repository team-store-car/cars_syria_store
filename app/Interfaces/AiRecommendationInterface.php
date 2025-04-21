<?php
namespace App\Interfaces;

interface AiRecommendationInterface
{
    /**
     * الحصول على قائمة السيارات المقترحة بناءً على إجابات المستخدم.
     * @param array $userAnswers بيانات إجابات المستخدم
     * @return array<int, mixed> قائمة بالتوصيات (قد تكون أسماء سيارات كنصوص، أو مصفوفات بيانات أكثر تفصيلاً)
     * @throws \Exception إذا فشل الاتصال أو التحليل
     */
    public function getSuggestions(array $userAnswers): array;
}
