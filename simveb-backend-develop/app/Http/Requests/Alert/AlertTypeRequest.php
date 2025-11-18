<?php

namespace App\Http\Requests\Alert;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class AlertTypeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('alert_types', 'name')->ignore($this->alert_type?->id)],
            'description' => ['nullable', 'string'],
            'image' => ['sometimes', 'required', File::image()],
            'code' => ['required', 'string', Rule::unique('alert_types', 'code')->ignore($this->alert_type?->id)],
        ];
    }
}
