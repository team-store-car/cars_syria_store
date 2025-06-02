<?php
namespace App\Http\Requests;

use App\Models\Workshop; // تأكد من استيراد Workshop
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInspectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'workshop_id' => [
                'required',
                'integer',
             
                Rule::exists('workshops', 'id')->where(function ($query) {
                   $query->whereNotNull('id'); 
                }),
            ],
            'preferred_datetime' => ['required', 'date', 'after:now'], 
            'notes' => ['nullable', 'string', 'max:1000'],

        ];
    }
}