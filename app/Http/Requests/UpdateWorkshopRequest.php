<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkshopRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string',
            'description' => 'sometimes|string|nullable',
            'city' => 'sometimes|string',
'commercial_registration_number' => 'sometimes|string|unique:workshops,commercial_registration_number,' . $this->id,
            'commercial_registration_image' => 'sometimes|url|nullable',
            'certification_details' => 'sometimes|string|nullable',
        ];
    }
    
}
