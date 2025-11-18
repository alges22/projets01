<?php

namespace App\Http\Requests\Institution;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstitutionRequest extends FormRequest
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
            'acronym' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', Rule::unique('institutions', 'name')->ignore($this->institution?->id)],
            'ifu' => ['nullable', 'digits:13', Rule::unique('institutions', 'ifu')->ignore($this->institution?->id)],
            'email' => ['nullable', 'email', 'string', Rule::unique('institutions', 'email')->ignore($this->institution?->id)],
            'telephone' => ['nullable', 'digits:8'],
            'address' => ['nullable', 'string', 'max:255'],
            'type_id' => ['required', 'exists:institution_types,id'],
            'town_id' => ['required', 'exists:towns,id'],
            'border_id' => ['nullable', 'exists:borders,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'logo' => ['nullable', 'image'],
        ];
    }
}
