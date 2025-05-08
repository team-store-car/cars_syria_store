<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Question;       
use App\Models\QuestionOption; 

/**
 * Test the endpoint for car recommendations with an actual call to the external API.
 */
class RealCarRecommendationApiTest extends TestCase
{
    use RefreshDatabase; 

    function test_endpoint_returns_real_ai_recommendations(): void
    {
        $q1 = Question::factory()->create(); 
        $q2 = Question::factory()->create();
        $opt1 = QuestionOption::factory()->create(['question_id' => $q1->id]);

        $opt2 = QuestionOption::factory()->create(['question_id' => $q2->id]);

        $requestData = [
            'answers' => [
                ['question_id' => $q1->id, 'chosen_option_id' => $opt1->id],
                ['question_id' => $q2->id, 'chosen_option_id' => $opt2->id],
            ]
        ];

        $response = $this->postJson(route('api.v1.car-recommendations.get'), $requestData);

        $response->assertStatus(200);
        $response->assertJsonStructure([ 
            'message',
            'data',   
        ]);
        $response->assertJsonIsArray('data');

        $responseData = $response->json('data');
        $this->assertNotEmpty($responseData, "The 'data' field returned by the API should not be empty.");

        // Optional: Verify the structure of the first recommendation item if it is consistent
        if (!empty($responseData)) {
             dump($responseData);
        }
    }
}
