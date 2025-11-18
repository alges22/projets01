<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TitleReasonRequest extends FormRequest
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
            'label' => ['required', 'string', Rule::unique('title_reasons', 'label')->ignore($this->title_reason?->id)],
            'description' => ['nullable', 'string'],
            'reason_type' => ['required', 'uuid', 'exists:title_reason_types,id'],
        ];
    }
}
