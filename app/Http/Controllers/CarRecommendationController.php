<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetCarRecommendationRequest;
use App\Services\CarRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Log\Logger;
use Throwable;

class CarRecommendationController extends Controller
{
    public function __construct(
        private readonly CarRecommendationService $recommendationService,
        private readonly Logger $logger
        ) {}

  
    public function getRecommendations(GetCarRecommendationRequest $request): JsonResponse
    {
        try {
            // Extract the validated data from the Form Request
            $validatedAnswers = $request->validated()['answers']; 

            $recommendations = $this->recommendationService->generateRecommendations($validatedAnswers);

            return response()->json([
                'message' => 'Car recommendations generated successfully.',
                'data' => $recommendations,
            ]);

        } catch (Throwable $e) {
            // Log the error to assist in debugging
            $this->logger->error('Car recommendation generation failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Be cautious when logging full trace in production
                'request_data' => $request->validated() // Do not log sensitive data
            ]);

            // Return a generic error response to the user
            return response()->json([
                'message' => 'Failed to generate car recommendations. Please try again later.',
   
            ], 500);
        }
    }
}