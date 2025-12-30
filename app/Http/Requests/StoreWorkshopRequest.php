<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkshopRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'city' => 'required|string',
            'commercial_registration_number' => 'required|string|unique:workshops',
            'commercial_registration_image' => 'nullable|url',
            'certification_details' => 'nullable|string',
            'owner_number' => 'nullable|string|max:20',
        ];
    }

}
