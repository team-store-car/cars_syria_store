<?php

namespace Tests\Feature;

use App\Interfaces\AiRecommendationInterface;
// إضافة use statements المطلوبة
use App\Models\Car;
// ازالة: use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Tests\TestCase;
use Exception;

class CarRecommendationTest extends TestCase
{
    use RefreshDatabase;


    public function test_anyone_can_get_recommendations_successfully(): void // تغيير اسم الاختبار ليعكس عدم الحاجة للمصادقة
    {
        // 1. Arrange
        // يجب أن يعمل هذا الآن بعد إضافة use App\Models\Car;
        Car::factory()->create(['name' => 'Matching Car 1', 'type' => 'Sedan', 'features' => json_encode(['navigation', 'leather'])]);
        Car::factory()->create(['name' => 'Matching Car 2', 'type' => 'Sedan', 'features' => json_encode(['navigation'])]);
        Car::factory()->create(['name' => 'Non-Matching Car', 'type' => 'SUV']);

        $requestData = [
            'answers' => [
                ['question_id' => 1, 'answer_value' => 'Commuting'],
                ['question_id' => 2, 'answer_value' => 'Needs navigation'],
            ]
        ];
        $expectedAiCriteria = ['types' => ['Sedan'], 'features' => ['navigation']];

        $this->mock(AiRecommendationInterface::class, function (MockInterface $mock) use ($expectedAiCriteria) {
            $mock->shouldReceive('getSuggestions')
                 ->once()
                 ->andReturn($expectedAiCriteria);
        });

        // 2. Act
        // ازالة: $this->actingAs($this->user, 'sanctum')
        $response = $this->postJson(route('api.v1.car-recommendations.get'), $requestData);

        // 3. Assert
        $response->assertStatus(200); // أو 201
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => ['id', 'name', 'type', 'features']
            ]
        ]);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['name' => 'Matching Car 1']);
        $response->assertJsonFragment(['name' => 'Matching Car 2']);
        $response->assertJsonMissing(['name' => 'Non-Matching Car']);
    }

     
    public function test_it_returns_validation_error_for_invalid_data(): void
    {
        // 1. Arrange
        $invalidRequestData = ['answers' => "not an array"];

        // 2. Act
         // ازالة: $this->actingAs($this->user, 'sanctum')
        $response = $this->postJson(route('api.v1.car-recommendations.get'), $invalidRequestData);

        // 3. Assert
        $response->assertStatus(422); // نتوقع 422 الآن بعد إصلاح سبب الـ 500 المحتمل
        $response->assertJsonValidationErrors(['answers']);
    }

    // ازالة: اختبار guest_user_cannot_get_recommendations بالكامل


    public function test_it_returns_server_error_when_ai_service_fails(): void
    {
        // 1. Arrange
        $requestData = [
            'answers' => [['question_id' => 1, 'answer_value' => 'Test']]
        ];

        $this->mock(AiRecommendationInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSuggestions')
                 ->once()
                 ->andThrow(new Exception('Simulated AI Service Failure'));
        });

        // 2. Act
         // ازالة: $this->actingAs($this->user, 'sanctum')
        $response = $this->postJson(route('api.v1.car-recommendations.get'), $requestData);

        // 3. Assert
        $response->assertStatus(500);
        $response->assertJson([
            'message' => 'Failed to generate car recommendations. Please try again later.'
        ]);
    }
}