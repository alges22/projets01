<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProvidedServiceRequest extends FormRequest
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
            'name' => ['string', Rule::unique('provided_services', 'name')->ignore($this->route()->parameter('provided_service'))],
            'description' => ['nullable', 'string'],
            'cost' => ['integer', 'min:0'],
            'duration' => ['nullable', 'string'],
            'required_documents' => ['nullable', 'string'],
            'image' => ['nullable', 'file']
        ];
    }
}
