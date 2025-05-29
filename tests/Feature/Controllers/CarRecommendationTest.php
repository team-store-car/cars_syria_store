<?php

namespace Tests\Feature;

use App\Interfaces\AiRecommendationInterface;
// إضافة use statements المطلوبة
use App\Models\Car;
// ازالة: use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Tests\TestCase;
use Exception;

class CarRecommendationTest extends TestCase
{
    use RefreshDatabase;


    public function test_anyone_can_get_recommendations_successfully(): void
    {
        // 1. Arrange
        // --- إنشاء الأسئلة والخيارات الوهمية اللازمة للتحقق ---
        $q1 = Question::factory()->create(['id' => 4]);
        $q2 = Question::factory()->create(['id' => 5]);
        $opt10 = QuestionOption::factory()->create(['id' => 10, 'question_id' => $q1->id]);
        $opt20 = QuestionOption::factory()->create(['id' => 20, 'question_id' => $q2->id]);
        // ----------------------------------------------------

        // بيانات الطلب الصالحة (باستخدام chosen_option_id)
        $requestData = [
            'answers' => [
                ['question_id' => $q1->id, 'chosen_option_id' => $opt10->id],
                ['question_id' => $q2->id, 'chosen_option_id' => $opt20->id],
            ]
        ];

        // القائمة المتوقع أن يرجعها الـ AI المحاكى
        $expectedAiRecommendations = ["Toyota Camry 2024", "Honda Accord 2023"];

        // محاكاة خدمة الـ AI
        $this->mock(AiRecommendationInterface::class, function (MockInterface $mock) use ($requestData, $expectedAiRecommendations) {
            // يمكنك التحقق من الوسيط (userAnswers) إذا أردت دقة أكبر
            $mock->shouldReceive('getSuggestions')
                 ->once()
                 ->andReturn($expectedAiRecommendations); // إرجاع قائمة السيارات مباشرة
        });

        // 2. Act
        $response = $this->postJson(route('api.v1.car-recommendations.get'), $requestData);

        // 3. Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([ // التحقق من بنية الـ JSON
            'message',
            'data' // نتوقع الآن أن data هي مصفوفة التوصيات مباشرة
        ]);
        // التحقق من أن البيانات المرجعة هي نفس مصفوفة التوصيات من الـ AI
        $response->assertJson([
            'data' => $expectedAiRecommendations
        ]);
        $response->assertJsonCount(count($expectedAiRecommendations), 'data');
    }

     
 
}