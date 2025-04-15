<?php
namespace App\Http\Requests;

use App\Models\Workshop; // تأكد من استيراد Workshop
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInspectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        // السماح فقط للمستخدمين المسجلين بإنشاء طلب
        return auth()->check();
        // يمكنك إضافة منطق أكثر تعقيدًا هنا (مثل التحقق من نوع المستخدم إذا لزم الأمر)
    }

    public function rules(): array
    {
        return [
            'workshop_id' => [
                'required',
                'integer',
                // التأكد من أن الورشة موجودة وربما "معتمدة" أو "نشطة"
                Rule::exists('workshops', 'id')->where(function ($query) {
                   // $query->where('is_approved', true)->where('is_active', true); // مثال
                   $query->whereNotNull('id'); // تحقق بسيط من الوجود أولاً
                }),
            ],
            'preferred_datetime' => ['required', 'date', 'after:now'], // يجب أن يكون تاريخًا مستقبليًا
            'notes' => ['nullable', 'string', 'max:1000'],
            // أضف أي حقول أخرى تحتاج للتحقق منها
        ];
    }
}