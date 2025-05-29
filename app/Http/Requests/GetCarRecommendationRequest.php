<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// افتراض وجود المودل QuestionOption
use App\Models\QuestionOption;
use Illuminate\Validation\Rule; // لاستخدام Rule::exists

class GetCarRecommendationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // السماح للجميع
    }

    public function rules(): array
    {
        return [
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
    'answers.*.chosen_option_id' => 'required|integer|exists:question_options,id',

            // --- استخدم هذا الكود ---
            'answers.*.answer_value' => [
                // لضمان وجود القيمة
                function ($attribute, $value, $fail) {
                    // is_scalar تتحقق إذا كانت القيمة string, integer, float, أو boolean
                    if (!is_scalar($value)) {
                        // يمكنك تخصيص رسالة الخطأ هنا
                        $fail("قيمة الإجابة المدخلة غير صالحة. يجب أن تكون نصاً أو رقماً أو قيمة منطقية.");
                        // أو بالإنجليزية:
                        // $fail('The :attribute must be a string, integer, float, or boolean.');
                    }
                },
            ],
            // --- نهاية الكود ---
        ];
    }
    

    public function messages(): array
    {
        return [
            'answers.required' => 'Please provide answers to the questionnaire.',
            'answers.array' => 'The answers must be submitted in an array format.',
            'answers.*.question_id.required' => 'Each answer must have a question ID.',
            'answers.*.question_id.exists' => 'Invalid question ID provided.',
            'answers.*.chosen_option_id.required' => 'Each answer must have a chosen option ID.',
            'answers.*.chosen_option_id.integer' => 'The chosen option ID must be an integer.',
            // لا يوجد رسالة افتراضية للـ closure rule، سيتم استخدام الرسالة الممررة لـ $fail
        ];
    }
}