<?php

namespace App\Services\AI;

use App\Interfaces\AiRecommendationInterface;
// ازالة: use App\Interfaces\QuestionRepositoryInterface;
// ازالة: use App\Interfaces\QuestionOptionRepositoryInterface;
// إضافة: استخدام المودلز مباشرة
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
    // ازالة: خصائص الـ Repositories
    // private QuestionRepositoryInterface $questionRepository;
    // private QuestionOptionRepositoryInterface $optionRepository;

    // تعديل الـ constructor لإزالة حقن الـ Repositories
    public function __construct()
    {
        $this->apiKey = config('ai.deepseek.api_key');
        $this->apiUrl = config('ai.deepseek.api_url');

        if (empty($this->apiKey) || empty($this->apiUrl)) {
            throw new \InvalidArgumentException('Deepseek API Key or URL is not configured.');
        }
        // ازالة: تخزين الـ Repositories
        // $this->questionRepository = $questionRepository;
        // $this->optionRepository = $optionRepository;
    }

    /**
     * @throws RequestException|Exception
     */
    public function getSuggestions(array $userAnswers): array
    {
        // 1. تنسيق الإجابات باستخدام نصوص الأسئلة والخيارات الفعلية
        $prompt = $this->formatPrompt($userAnswers); // ستستخدم هذه الدالة المودلز مباشرة الآن
        if (empty($prompt)) {
            Log::warning('Could not format prompt from user answers.', ['answers' => $userAnswers]);
            return [];
        }

        // ... (باقي الكود لإرسال الطلب وتحليل الاستجابة يبقى كما هو) ...
        Log::debug('Sending prompt to Deepseek for car list', ['prompt' => $prompt]);
        $response = Http::withToken($this->apiKey)
                      ->timeout(30)
                      ->post($this->apiUrl, [
                          'model' => config('ai.deepseek.model', 'deepseek-chat'),
                           'messages' => [
                               ['role' => 'system', 'content' => 'You are a helpful assistant. Based on user preferences, suggest a list of specific car models (e.g., "Toyota Camry 2023", "Honda CR-V"). Provide only the list, perhaps one car per line or as a JSON list.'],
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
     * تنسيق الإجابات في شكل نص (prompt) لـ API باستخدام نصوص الأسئلة والخيارات.
     * **تستخدم الآن Eloquent Models مباشرة.**
     */
    private function formatPrompt(array $userAnswers): string
    {
        $promptLines = [];
        $questionIds = array_column($userAnswers, 'question_id');
        $optionIds = array_column($userAnswers, 'chosen_option_id');

        // جلب نصوص الأسئلة والخيارات دفعة واحدة باستخدام المودلز
        // استخدام ->get()->keyBy('id') فعال لجلب البيانات مرة واحدة وتنظيمها للوصول السريع
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');
        $options = QuestionOption::whereIn('id', $optionIds)->get()->keyBy('id');


        foreach ($userAnswers as $answer) {
            $questionId = $answer['question_id'];
            $optionId = $answer['chosen_option_id'];

            $questionText = $questions->get($questionId)?->text; // افترض أن الحقل هو 'text'
            $optionText = $options->get($optionId)?->text; // افترض أن الحقل هو 'text'

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
     * تحليل استجابة الـ AI لاستخراج قائمة السيارات.
     * (نفس الكود السابق)
     */
    private function parseAiResponseForCarList(?string $responseText): array
    {
        // ... (نفس الكود السابق لتحليل الاستجابة) ...
        if (empty($responseText)) { return []; }
        $cars = explode("\n", trim($responseText));
        $cars = array_map('trim', $cars);
        $cars = array_filter($cars, function($line) {
            return !empty($line) && strlen($line) > 5 && !preg_match('/^[\d\-\*\.]+\s*/', $line);
        });
        return array_values($cars);
    }
}