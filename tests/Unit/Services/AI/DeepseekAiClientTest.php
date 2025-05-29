<?php

namespace Tests\Unit\Services\AI;

use App\Services\AI\DeepseekAiClient;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Config; // للتحكم في الإعدادات
use Illuminate\Support\Facades\Http; // لاستخدام Http Fake
use Tests\TestCase; // استخدام TestCase الخاص بـ Laravel للوصول لـ Http Fake وغيرها
use Exception; // للتحقق من الأخطاء

class DeepseekAiClientTest extends TestCase // لاحظ استخدام TestCase الخاص بـ Laravel
{
    private string $testApiUrl = 'https://api.deepseek.com/v1/test-chat';
    private string $testApiKey = 'test-api-key';
    private string $testModel = 'deepseek-test-model';

    // إعداد الإعدادات الوهمية قبل كل اختبار
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('ai.deepseek.api_key', $this->testApiKey);
        Config::set('ai.deepseek.api_url', $this->testApiUrl);
        Config::set('ai.deepseek.model', $this->testModel);
    }

    /**
     * اختبار الحصول على الاقتراحات بنجاح.
     * @test
     */
    public function it_gets_suggestions_successfully(): void
    {
        // 1. Arrange
        $userAnswers = [['question_id' => 1, 'answer_value' => 'Need a fuel-efficient car']];
        $expectedAiResponse = [ // شكل الاستجابة المتوقعة من API
            'id' => 'chatcmpl-123',
            'object' => 'chat.completion',
            'created' => 1677652288,
            'model' => $this->testModel,
            'choices' => [[
                'index' => 0,
                'message' => [
                    'role' => 'assistant',
                    // الاستجابة النصية التي سنقوم بتحليلها
                    'content' => "Recommend: type=Hybrid, features=[fuel_efficient, compact]",
                ],
                'finish_reason' => 'stop',
            ]],
            'usage' => ['prompt_tokens' => 9, 'completion_tokens' => 12, 'total_tokens' => 21],
        ];

        // محاكاة استجابة ناجحة من API
        Http::fake([
            $this->testApiUrl => Http::response($expectedAiResponse, 200),
        ]);

        $client = new DeepseekAiClient();

        // 2. Act
        $suggestions = $client->getSuggestions($userAnswers);

        // 3. Assert
        // التأكد من أن الطلب أُرسل إلى الـ URL الصحيح وبالبيانات الصحيحة
        Http::assertSent(function ($request) use ($userAnswers) {
            $prompt = $this->invokeMethod($this->app->make(DeepseekAiClient::class), 'formatPrompt', [$userAnswers]); // استدعاء الدالة الخاصة للتحقق من الـ Prompt
            return $request->url() === $this->testApiUrl &&
                   $request->method() === 'POST' &&
                   $request->hasHeader('Authorization', 'Bearer ' . $this->testApiKey) &&
                   $request['model'] === $this->testModel && // التحقق من بارامترات الطلب
                   $request['messages'][1]['content'] === $prompt; // التحقق من الـ Prompt المرسل
        });

        // التأكد من أن النتيجة التي تم تحليلها صحيحة
        // (يعتمد على منطق parseAiResponse - قد تحتاج لتعديل هذا)
        $this->assertIsArray($suggestions);
        $this->assertArrayHasKey('types', $suggestions);
        $this->assertContains('Hybrid', $suggestions['types']); // مثال بسيط
        $this->assertArrayHasKey('features', $suggestions);
        $this->assertContains('fuel_efficient', $suggestions['features']); // مثال بسيط
    }

    /**
     * اختبار الحالة عندما يفشل طلب الـ API.
     * @test
     */
    public function it_throws_exception_on_api_failure(): void
    {
        // 1. Arrange
        $userAnswers = [['question_id' => 1, 'answer_value' => 'Test']];

        // محاكاة استجابة خطأ من API
        Http::fake([
            $this->testApiUrl => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $client = new DeepseekAiClient();

        // 2. Assert Exception
        $this->expectException(RequestException::class); // توقع خطأ HTTP Client

        // 3. Act
        $client->getSuggestions($userAnswers);
    }

     /**
     * اختبار الحالة عندما تكون الاستجابة ناجحة لكن لا يمكن تحليلها.
     * @test
     */
    public function it_throws_exception_when_response_parsing_fails(): void
    {
        // 1. Arrange
        $userAnswers = [['question_id' => 1, 'answer_value' => 'Test']];
        $malformedAiResponse = [ // استجابة غير متوقعة الشكل
             'choices' => [[
                 'message' => ['content' => null] // محتوى فارغ أو غير قابل للتحليل
             ]]
        ];

        Http::fake([
            $this->testApiUrl => Http::response($malformedAiResponse, 200),
        ]);

        $client = new DeepseekAiClient();

        // 2. Assert Exception
         // توقع خطأ عام لأن التحليل فشل داخل الكلاينت
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Could not process AI recommendations.');


        // 3. Act
        $client->getSuggestions($userAnswers);
    }


     /**
     * Helper method to call private/protected methods for testing.
     * استخدام Reflection للوصول للدوال الخاصة بغرض الاختبار (للتحقق من formatPrompt)
     *
     * @param object $object
     * @param string $methodName
     * @param array  $parameters
     * @return mixed
     */
    protected function invokeMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true); // جعل الدالة قابلة للاستدعاء من الخارج

        return $method->invokeArgs($object, $parameters);
    }
}