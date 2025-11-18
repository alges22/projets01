<?php

namespace App\Http\Requests\Accreditation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccreditationRequest extends FormRequest
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
        $this->merge(['author_id' => getOnlineProfile()->id]);
        return [
            'author_id' => ['required', 'uuid', 'exists:profiles,id'],
            'receiver_id' => ['required_with:roles,permissions', 'uuid', 'exists:profiles,id'],
            'roles' => ['nullable', Rule::requiredIf(empty($this->permissions)), 'array'],
            'roles.*' => ['required', 'numeric', 'exists:roles,id'],
            'permissions' => ['nullable', Rule::requiredIf(empty($this->roles)), 'array'],
            'permissions.*' => ['required', 'numeric', 'exists:permissions,id'],
        ];
    }
}
