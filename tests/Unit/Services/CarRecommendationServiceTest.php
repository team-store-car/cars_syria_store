<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

use App\Interfaces\AiRecommendationInterface;
use App\Services\CarRecommendationService;
use Illuminate\Support\Facades\Log; 
use Mockery;
use Mockery\MockInterface;
use Exception;


class CarRecommendationServiceTest extends TestCase
{

   
    public function test_it_successfully_generates_recommendations(): void
    {
        // 1. Arrange
        $userAnswers = [['question_id' => 1, 'answer_value' => 'Family Use']];
        $expectedRecommendationsArray = [
            ['name' => 'Recommended Car 1', 'details' => '...'],
            ['name' => 'Recommended Car 2', 'details' => '...'],
        ];

        /** @var AiRecommendationInterface&MockInterface $aiServiceMock */
        $aiServiceMock = Mockery::mock(AiRecommendationInterface::class);

        $aiServiceMock->shouldReceive('getSuggestions')
            ->once()
            ->with($userAnswers)
            ->andReturn($expectedRecommendationsArray);


        Log::shouldReceive('info')->atLeast()->once(); 
         $this->instance(AiRecommendationInterface::class, $aiServiceMock); 
         $service = $this->app->make(CarRecommendationService::class); 
        

        // 2. Act
        $recommendations = $service->generateRecommendations($userAnswers);

        // 3. Assert
        $this->assertIsArray($recommendations);
        $this->assertEquals($expectedRecommendationsArray, $recommendations);
    }

  
    public function test_it_returns_empty_array_when_ai_provides_no_criteria(): void 
    {
        // 1. Arrange
        $userAnswers = [['question_id' => 1, 'answer_value' => 'Something vague']];
        $emptyArray = []; // الـ AI لم يرجع شيئاً

        /** @var AiRecommendationInterface&MockInterface $aiServiceMock */
        $aiServiceMock = Mockery::mock(AiRecommendationInterface::class);

        $aiServiceMock->shouldReceive('getSuggestions')
            ->once()
            ->with($userAnswers)
            ->andReturn($emptyArray);


        Log::shouldReceive('info')->atLeast()->once();

        $this->instance(AiRecommendationInterface::class, $aiServiceMock);
        $service = $this->app->make(CarRecommendationService::class);

        // 2. Act
        $recommendations = $service->generateRecommendations($userAnswers);

        // 3. Assert
        $this->assertIsArray($recommendations);
        $this->assertEmpty($recommendations);
    }

    
     public function test_it_throws_exception_when_ai_service_fails(): void
     {
         // 1. Arrange
         $userAnswers = [['question_id' => 1, 'answer_value' => 'Test']];

         /** @var AiRecommendationInterface&MockInterface $aiServiceMock */
         $aiServiceMock = Mockery::mock(AiRecommendationInterface::class);

         $aiServiceMock->shouldReceive('getSuggestions')
             ->once()
             ->with($userAnswers)
             ->andThrow(new Exception('AI API Error'));


         $this->instance(AiRecommendationInterface::class, $aiServiceMock);
         $service = $this->app->make(CarRecommendationService::class);


         // 2. Assert Exception
         $this->expectException(Exception::class);
         $this->expectExceptionMessage('AI API Error');

         // 3. Act
         $service->generateRecommendations($userAnswers);
     }
}