<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarOfferRequest extends FormRequest
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
        $rules = [
            'offer_type' => 'required|in:sale,rent',
            'price' => 'required|numeric|min:0',
            'price_unit' => 'sometimes|string|in:SAR,USD',
            'location'=>'required|string',
            'pricing_period' => 'required_if:offer_type,rent|nullable|in:daily,weekly,monthly',
            'additional_features' => 'nullable|string',
        ];

        if ($this->isMethod('GET')) {
            $rules = [
                'offer_type' => 'sometimes|in:sale,rent',
                'price_min' => 'sometimes|numeric|min:0',
                'price_max' => 'sometimes|numeric|min:0',
            ];
        }

        return $rules;
    }
}
