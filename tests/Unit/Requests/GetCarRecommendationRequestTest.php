<?php


use App\Http\Requests\GetCarRecommendationRequest;
use App\Models\User; // لاستخدامه في اختبار الصلاحيات
use Illuminate\Support\Facades\Auth; // لمحاكاة تسجيل الدخول
use Illuminate\Support\Facades\Validator; // لاختبار القواعد
use Tests\TestCase; // استخدام TestCase الخاص بـ Laravel
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Models\Question;

class GetCarRecommendationRequestTest extends TestCase
{
    use RefreshDatabase ;
    private GetCarRecommendationRequest $request;


    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new GetCarRecommendationRequest();

        // --- تأكد من وجود هذا الكود ---
        // وتأكد من وجود QuestionFactory يعمل بشكل صحيح
        Question::factory()->create(['id' => 1]);
        Question::factory()->create(['id' => 2]);
        Question::factory()->create(['id' => 3]); // أنشئ السؤال 3 أيضاً لاختبار البيانات الأخرى
        // --- نهاية الكود ---
    }
   
    /**
     * اختبار نجاح التحقق ببيانات صالحة.
     * @dataProvider validDataProvider
     * @test
     */
    public function validation_passes_with_valid_data(array $data): void
    {
        $validator = Validator::make($data, $this->request->rules());
    
        // --- أضف هذا الكود للتحقق ---
        if ($validator->fails()) {
            // اطبع الأخطاء وتوقف لمعرفة السبب
            dump("Validation failed for data: ", $data);
            dd($validator->errors()->toArray());
        }
        // --- نهاية كود التحقق ---
    
        $this->assertFalse($validator->fails()); // يجب أن لا يفشل
    }
    

    /**
     * اختبار فشل التحقق ببيانات غير صالحة.
     * @dataProvider invalidDataProvider
     * @test
     */

    // --- Data Providers ---

    /**
     * بيانات صالحة لاختبار التحقق.
     */
    public static function validDataProvider(): array
    {
        return [
            'بيانات كاملة وصحيحة' => [
                [
                    'answers' => [
                        [
                            'question_id' => 1,
                            'answer_value' => 'قيمة صحيحة'
                        ],
                        [
                            'question_id' => 2,
                            'answer_value' => 10
                        ]
                    ]
                ]
            ],
            'إجابة واحدة صحيحة' => [
                [
                    'answers' => [
                        [
                            'question_id' => 3,
                            'answer_value' => true
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * بيانات غير صالحة لاختبار التحقق والقواعد المتوقع فشلها.
     */
    public static function invalidDataProvider(): array
    {
        return [
            'عنصر answers بدون answer value' => [
                [
                    'answers' => [
                        ['question_id' => 1]
                    ]
                ],
                'answers.0.answer_value' // ← يجب أن يتطابق مع اسم الحقل في القواعد
            ],
            'عنصر answers بقيم خاطئة النوع' => [
                [
                    'answers' => [
                        [
                            'question_id' => 'نص', // يجب أن يكون رقمًا
                            'answer_value' => []
                        ]
                    ]
                ],
                ['answers.0.question_id', 'answers.0.answer_value']
            ]
        ];
    }
}