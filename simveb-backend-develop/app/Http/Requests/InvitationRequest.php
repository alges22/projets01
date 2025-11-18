<?php

namespace App\Http\Requests;

use App\Rules\UniqueNpiSpaceRule;
use App\Rules\ValidateGuestNpiRule;
use Illuminate\Foundation\Http\FormRequest;

class InvitationRequest extends FormRequest
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
            'npi' => ['required', 'string', 'size:10', new UniqueNpiSpaceRule($this->space_id)],
            'space_id' => ['nullable', 'exists:spaces,id'],
            'profile_type_id' => ['nullable', 'exists:profile_types,id'],
            'roles' => ['nullable', 'array', 'min:0'],
            'roles.*' => ['required', 'exists:roles,id',],
        ];
    }
}
