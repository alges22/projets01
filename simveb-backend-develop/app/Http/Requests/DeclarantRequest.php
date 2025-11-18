<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeclarantRequest extends FormRequest
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
            "firstname" => ['required', 'string', 'max:255'],
            "lastname" => ['required', 'string', 'max:255'],
            "telephone" => ['required', 'string', 'max:12', Rule::unique("identities","telephone")->ignore(optional(optional($this->declarant)->identity)->id)],
            "email" => ['required', 'email', Rule::unique("identities","email")->ignore(optional(optional($this->declarant)->identity)->id)],
            "user_id" => ['exists:users,id'],
            "institution_id" => ['required', 'exists:institutions,id']
        ];
    }
}
