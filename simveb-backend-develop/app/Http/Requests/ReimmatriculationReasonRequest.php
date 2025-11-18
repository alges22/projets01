<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReimmatriculationReasonRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', Rule::unique('reimmatriculation_reasons', 'title')->ignore($this->reimmatriculation_reason?->id)],
            'requires_reason' => ['required', 'boolean'],
            'requires_title_deposit' => ['required', 'boolean'],
            'requires_transfer_certificate' => ['required', 'boolean'],
        ];
    }
}
