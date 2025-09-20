<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterWorkshopAdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'price' => 'nullable|array',
            'price.min' => 'nullable|numeric|min:0',
            'price.max' => 'nullable|numeric|min:0',
            'workshop_id' => 'nullable|integer|exists:workshops,id',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }
}
