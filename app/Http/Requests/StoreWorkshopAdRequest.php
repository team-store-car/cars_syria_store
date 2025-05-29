<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkshopAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يمكن تعديلها لاحقًا للتحقق من صلاحيات المستخدم
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ];
    }
}
