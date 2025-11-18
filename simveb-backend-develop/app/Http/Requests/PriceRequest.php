<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PriceRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required','exists:services,id'],
            'characteristic_id' => ['required','exists:vehicle_characteristics,id'],
            'owner_type_id' => ['required','exists:owner_types,id'],
            'vehicle_type_id' => ['required','exists:vehicle_types,id'],
            'vehicle_category_id' => ['required','exists:vehicle_categories,id'],
            'price' => ['required','min:0','numeric']
        ];
    }
}
