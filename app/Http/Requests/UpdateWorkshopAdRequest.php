<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkshopAdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // التحقق من الصلاحية الأساسية يمكن أن يتم هنا
        // لكن التحقق من ملكية الإعلان المحدد يتم بشكل أفضل في Controller أو Service
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // نفس قواعد الإنشاء أو يمكن تعديلها حسب الحاجة
        // مثل جعل بعض الحقول اختيارية باستخدام 'sometimes' أو 'nullable'
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
        ];
        /* مثال لجعلها اختيارية:
         return [
             'title' => 'sometimes|string|max:255', // سيتم التحقق فقط إذا كان الحقل موجودًا
             'description' => 'sometimes|string',
             'price' => 'sometimes|numeric|min:0',
         ];
        */
    }
}