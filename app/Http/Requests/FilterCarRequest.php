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
            'fuel_type' => 'nullable',
            'transmission' => 'nullable|in:manual,automatic',
            'horsepower' => 'nullable|array',
            'horsepower.min' => 'nullable|integer|min:0',
            'horsepower.max' => 'nullable|integer|min:0',
            'seats' => 'nullable|integer|min:1',
            'color' => 'nullable|string|max:255',
            'is_featured' => 'nullable',
            'user_id' => 'nullable|exists:users,id',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'store_id' => 'nullable|integer|exists:stores,id',
            'price' => 'nullable|array',
            'price.min' => 'nullable|numeric|min:0',
            'price.max' => 'nullable|numeric|min:0',
            'offer_type' => 'nullable|string|in:sale,rent', // Adjust based on your offer types
            'price_unit' => 'nullable|string|in:USD,SAR', // Adjust based on your currencies
            'location' => 'nullable|string|max:255',
            'pricing_period' => 'nullable|string|in:daily,weekly,monthly,yearly', // Adjust based on your periods
            'is_available' => 'nullable|boolean',
            'additional_features' => 'nullable|string|max:255',
        ];
    }
}
