<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImmatriculationTypeRequest extends FormRequest
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
            'label' => ['required', 'string', Rule::unique('immatriculation_types', 'label')->ignore($this->immatriculation_type?->id)],
            'code' => ['required', 'string', Rule::unique('immatriculation_types', 'code')->ignore($this->immatriculation_type?->id)],
            'description' => ['nullable', 'string'],
            'plate_colors' => ['required', 'array'],
            'plate_colors.*' => [
                'required',
                'uuid',
                Rule::exists('plate_colors', 'id'),
            ],
        ];
    }
}
