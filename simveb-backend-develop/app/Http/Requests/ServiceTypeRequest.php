<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceTypeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //'code' => ['required','string',Rule::unique('service_types','code')->ignore($this->service_type?->id)],
            'name' => ['required','string',Rule::unique('service_types','name')->ignore($this->service_type?->id)],
            'description' => ['string','nullable'],
            'cost' => ['numeric','nullable','min:0'],
        ];
    }
}
