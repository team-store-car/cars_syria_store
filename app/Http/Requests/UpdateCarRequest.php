<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
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
            'name' => 'sometimes|string|unique:cars,name,' ,
            'user_id'=> 'sometimes|exists:users,id',
            'brand' => 'sometimes|string|max:100',
            'category_id' => 'sometimes|exists:categories,id',
            'country_of_manufacture' => 'sometimes|string|max:100',
            'model' => 'sometimes|string|max:100',
            'year' => 'sometimes|integer|min:1886|max:' . date('Y'),
            'condition' => 'sometimes|in:new,used',
            'mileage' => 'nullable|integer|min:0',
            'fuel_type' => 'sometimes|string|max:50',
            'transmission' => 'sometimes|string|max:50',
            'horsepower' => 'nullable|integer|min:0',
            'seats' => 'sometimes|integer|min:1',
            'color' => 'sometimes|string|max:50',
            'description' => 'nullable|string',
            'is_featured' => 'sometimes|boolean',
            'other_benefits' => 'nullable|string',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'alt_texts' => 'sometimes|array',
            'alt_texts.*' => 'nullable|string|max:255',
        ];
    }
}
