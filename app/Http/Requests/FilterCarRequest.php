<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterCarRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'country_of_manufacture' => 'nullable|string|max:255',
            'condition' => 'nullable|in:new,used,certified_pre_owned',
            'mileage' => 'nullable|array',
            'mileage.min' => 'nullable|integer|min:0',
            'mileage.max' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid',
            'transmission' => 'nullable|in:manual,automatic',
            'horsepower' => 'nullable|array',
            'horsepower.min' => 'nullable|integer|min:0',
            'horsepower.max' => 'nullable|integer|min:0',
            'seats' => 'nullable|integer|min:1',
            'color' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
