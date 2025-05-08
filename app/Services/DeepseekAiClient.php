<?php

namespace App\Services;

use App\Interfaces\AiRecommendationInterface;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DeepseekAiClient implements AiRecommendationInterface
{
    private string $apiKey;
    private string $apiUrl;
    public function __construct()
    {
        $this->apiKey = config('ai.deepseek.api_key');
        $this->apiUrl = config('ai.deepseek.api_url');

        if (empty($this->apiKey) || empty($this->apiUrl)) {
            throw new \InvalidArgumentException('Deepseek API Key or URL is not configured.');
        }
    }

    /**
     * @throws RequestException|Exception
     */
    public function getSuggestions(array $userAnswers): array
    {
        // 1. Format the answers using the actual question and option texts
        $prompt = $this->formatPrompt($userAnswers); // This function now directly uses the models
        if (empty($prompt)) {
            Log::warning('Could not format prompt from user answers.', ['answers' => $userAnswers]);
            return [];
        }

        // ... (The rest of the code for sending the request and parsing the response remains the same) ...
        Log::debug('Sending prompt to Deepseek for car list', ['prompt' => $prompt]);
        $response = Http::withToken($this->apiKey)
                      ->timeout(30)
                      ->post($this->apiUrl, [
                          'model' => config('ai.deepseek.model', 'deepseek-chat'),
                           'messages' => [
                               ['role' => 'system', 'content' => 'Suggest suitable cars based on these user-selected data. Remember, I want concise answers only.'],
                               ['role' => 'user', 'content' => $prompt]
                           ],
                      ]);

        if ($response->failed()) {
            Log::error('Deepseek API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
            $response->throw();
        }
        try {
            $aiResponseContent = $response->json('choices.0.message.content');
            Log::debug('Received response from Deepseek for car list', ['content' => $aiResponseContent]);
            return $this->parseAiResponseForCarList($aiResponseContent);
        } catch (\Throwable $e) {
            Log::error('Failed to parse Deepseek API response for car list.', [
               'response_body' => $response->body(), 'error' => $e->getMessage()
            ]);
           throw new Exception('Could not process AI recommendations.', 0, $e);
        }
    }

    /**
     * Format the answers into a text (prompt) for the API using the question and option texts.
     * **Now directly uses Eloquent Models.**
     */
    private function formatPrompt(array $userAnswers): string
    {
        $promptLines = [];
        $questionIds = array_column($userAnswers, 'question_id');
        $optionIds = array_column($userAnswers, 'chosen_option_id');

        // Fetch question and option texts in bulk using models
        // Using ->get()->keyBy('id') is efficient for fetching and organizing data for quick access
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');
        $options = QuestionOption::whereIn('id', $optionIds)->get()->keyBy('id');


        foreach ($userAnswers as $answer) {
            $questionId = $answer['question_id'];
            $optionId = $answer['chosen_option_id'];

            $questionText = $questions->get($questionId)?->text; // Assume the field is 'text'
            $optionText = $options->get($optionId)?->text; // Assume the field is 'text'

            if ($questionText && $optionText) {
                $promptLines[] = "- " . $questionText . ": " . $optionText;
            } else {
                 Log::warning('Could not find text for question or option using Models', ['question_id' => $questionId, 'option_id' => $optionId]);
            }
        }

        if(empty($promptLines)) return "";

        $prompt = "User Preferences:\n";
        $prompt .= implode("\n", $promptLines);
        $prompt .= "\n\nBased on these preferences, list suitable car models (make, model, optionally year).";

        return $prompt;
    }

    /**
     * Parse the AI response to extract the car list.
     * (Same as the previous code)
     */
    private function parseAiResponseForCarList(?string $responseText): array
    {
        // ... (Same as the previous code for parsing the response) ...
        if (empty($responseText)) { return []; }
        $cars = explode("\n", trim($responseText));
        $cars = array_map('trim', $cars);
        $cars = array_filter($cars, function($line) {
            return !empty($line) && strlen($line) > 5 && !preg_match('/^[\d\-\*\.]+\s*/', $line);
        });
        return array_values($cars);
    }
}