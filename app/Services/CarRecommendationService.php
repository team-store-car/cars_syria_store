<?php
namespace App\Services;

use App\Interfaces\AiRecommendationInterface;
use Illuminate\Support\Collection; 
use Illuminate\Support\Facades\Log;

class CarRecommendationService
{
    public function __construct(
        private readonly AiRecommendationInterface $aiService
    ) {}

    /**
     * @throws \Exception
     */
    public function generateRecommendations(array $userAnswers): array 
    {
        Log::info('Generating car recommendations based on answers.', ['answers_count' => count($userAnswers)]);

        //  Retrieve the list of suggested cars directly from the AI service
        $recommendations = $this->aiService->getSuggestions($userAnswers);
        Log::info('AI generated recommendations.', ['recommendations' => $recommendations]);


        return $recommendations; 
    }
}
