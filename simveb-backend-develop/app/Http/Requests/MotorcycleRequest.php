<?php

namespace App\Http\Requests;

use App\Rules\Demands\ValideNpiRule;
use Illuminate\Foundation\Http\FormRequest;

class MotorcycleRequest extends FormRequest
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
            'customs_reference' => ['required','string'],
            'vin' => ['nullable','string','unique:motorcycles,vin'],
            'npi' => ['nullable', new ValideNpiRule],
        ];
    }
}
