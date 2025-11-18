<?php

namespace App\Http\Requests\Immatriculation;

use Illuminate\Foundation\Http\FormRequest;

class PrintImmatriculationRequest extends FormRequest
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
            'treatment_id' => ['required','exists:treatments,id'],
            'print_observations' => ['nullable','string']
        ];
    }
}
