<?php

namespace Tests\Unit\Services;

use App\Interfaces\AiRecommendationInterface;
use App\Interfaces\CarRepositoryInterface;
use App\Models\Car; // لاستخدامه في Collection
use App\Services\CarRecommendationService;
use Illuminate\Support\Collection; // للتعامل مع Collection
use Mockery; // لاستخدام Mockery
use Mockery\MockInterface; // تحديد نوع Mock
use PHPUnit\Framework\TestCase; // استخدام TestCase الأساسي لـ Unit Tests
use Exception; // لاختبار حالة الخطأ

class CarRecommendationServiceTest extends TestCase
{
    // تدمير كائنات Mockery بعد كل اختبار لتجنب التداخل
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * اختبار الحالة الناجحة لتوليد التوصيات.
     * @test
     */
    public function it_successfully_generates_recommendations(): void
    {
        // 1. Arrange (الترتيب)
        $userAnswers = [['question_id' => 1, 'answer_value' => 'Family Use'], ['question_id' => 2, 'answer_value' => 'Budget under 20k']];
        $expectedCriteria = ['type' => 'SUV', 'max_price' => 20000];
        // إنشاء Collection وهمية من موديلات Car
        $expectedCars = new Collection([
            new Car(['id' => 1, 'name' => 'Test SUV 1', 'type' => 'SUV', 'price' => 18000]),
            new Car(['id' => 2, 'name' => 'Test SUV 2', 'type' => 'SUV', 'price' => 19500]),
        ]);

        // محاكاة الواجهات
        /** @var AiRecommendationInterface&MockInterface $aiServiceMock */
        $aiServiceMock = Mockery::mock(AiRecommendationInterface::class);
        /** @var CarRepositoryInterface&MockInterface $carRepositoryMock */
        $carRepositoryMock = Mockery::mock(CarRepositoryInterface::class);

        // تحديد توقعات المحاكاة (Expectations)
        $aiServiceMock->shouldReceive('getSuggestions')
            ->once() // التأكد من استدعاء الدالة مرة واحدة
            ->with($userAnswers) // التأكد من استدعائها بنفس الإجابات
            ->andReturn($expectedCriteria); // تحديد القيمة المُرجعة المتوقعة

        $carRepositoryMock->shouldReceive('findMatchingCars')
            ->once()
            ->with($expectedCriteria) // التأكد من استدعائها بنفس المعايير
            ->andReturn($expectedCars); // تحديد القيمة المُرجعة المتوقعة

        // إنشاء الخدمة مع الاعتماديات المحاكاة
        $service = new CarRecommendationService($aiServiceMock, $carRepositoryMock);

        // 2. Act (التنفيذ)
        $recommendations = $service->generateRecommendations($userAnswers);

        // 3. Assert (التحقق)
        $this->assertInstanceOf(Collection::class, $recommendations);
        $this->assertEquals($expectedCars, $recommendations);
        // يمكنك إضافة تأكيدات إضافية هنا حسب الحاجة
    }

    /**
     * اختبار الحالة عندما تعيد خدمة الـ AI معايير فارغة.
     * @test
     */
    public function it_returns_empty_collection_when_ai_provides_no_criteria(): void
    {
        // 1. Arrange
        $userAnswers = [['question_id' => 1, 'answer_value' => 'Something vague']];
        $emptyCriteria = []; // الـ AI لم يستطع تحديد معايير

        /** @var AiRecommendationInterface&MockInterface $aiServiceMock */
        $aiServiceMock = Mockery::mock(AiRecommendationInterface::class);
        /** @var CarRepositoryInterface&MockInterface $carRepositoryMock */
        $carRepositoryMock = Mockery::mock(CarRepositoryInterface::class); // لا نتوقع استدعاءه

        $aiServiceMock->shouldReceive('getSuggestions')
            ->once()
            ->with($userAnswers)
            ->andReturn($emptyCriteria);

        // لا نتوقع استدعاء findMatchingCars أبداً
        $carRepositoryMock->shouldNotReceive('findMatchingCars');

        $service = new CarRecommendationService($aiServiceMock, $carRepositoryMock);

        // 2. Act
        $recommendations = $service->generateRecommendations($userAnswers);

        // 3. Assert
        $this->assertInstanceOf(Collection::class, $recommendations);
        $this->assertTrue($recommendations->isEmpty());
    }

     /**
     * اختبار الحالة عندما تفشل خدمة الـ AI (ترمي Exception).
     * @test
     */
    public function it_throws_exception_when_ai_service_fails(): void
    {
        // 1. Arrange
        $userAnswers = [['question_id' => 1, 'answer_value' => 'Test']];

        /** @var AiRecommendationInterface&MockInterface $aiServiceMock */
        $aiServiceMock = Mockery::mock(AiRecommendationInterface::class);
        /** @var CarRepositoryInterface&MockInterface $carRepositoryMock */
        $carRepositoryMock = Mockery::mock(CarRepositoryInterface::class); // لا نتوقع استدعاءه

        $aiServiceMock->shouldReceive('getSuggestions')
            ->once()
            ->with($userAnswers)
            ->andThrow(new Exception('AI API Error')); // محاكاة رمي خطأ

        $service = new CarRecommendationService($aiServiceMock, $carRepositoryMock);

        // 2. Assert Exception (التوقع + التنفيذ)
        $this->expectException(Exception::class); // توقع حدوث هذا النوع من الأخطاء
        $this->expectExceptionMessage('AI API Error'); // توقع رسالة الخطأ

        // 3. Act
        $service->generateRecommendations($userAnswers); // هذا السطر يجب أن يرمي الخطأ
    }
}