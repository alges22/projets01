<?php

namespace App\Http\Requests\ImpressionDemand;

use Illuminate\Foundation\Http\FormRequest;

class InitImpressionDemandRequest extends FormRequest
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
            'reference' => ['required', 'exists:demands,reference'],
            // adapt this to other demand types (other than immatriculation deman)
        ];
    }
}
