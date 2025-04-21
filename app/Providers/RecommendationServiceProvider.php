<?php

namespace App\Providers;

use App\Interfaces\AiRecommendationInterface;

use App\Services\AI\DeepseekAiClient;
use Illuminate\Support\ServiceProvider;

class RecommendationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // ربط الواجهة الخاصة بخدمة الـ AI بالتطبيق الملموس (Deepseek client)
        $this->app->bind(AiRecommendationInterface::class, DeepseekAiClient::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}