<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
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
            'name' => 'required|string|unique:cars,name|max:255',
            'user_id'=>'required|exists:users,id',
            'brand' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'country_of_manufacture' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1886|max:' . date('Y'),
            'condition' => 'required|in:new,used',
            'mileage' => 'nullable|integer|min:0',
            'fuel_type' => 'required|string|max:50',
            'transmission' => 'required|string|max:50',
            'horsepower' => 'nullable|integer|min:0',
            'seats' => 'required|integer|min:1',
            'color' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
            'other_benefits' => 'nullable|string',
        ];
    }
}
