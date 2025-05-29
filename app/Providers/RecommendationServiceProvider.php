<?php

namespace App\Providers;

use App\Interfaces\AiRecommendationInterface;
use App\Services\DeepseekAiClient; 
use Illuminate\Support\ServiceProvider;
// ازالة: use App\Services\CarRecommendationService;

class RecommendationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AiRecommendationInterface::class,
            DeepseekAiClient::class 
        );

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}