<?php

namespace App\Http\Requests;

use App\Rules\UniqueLabelSlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Ntech\UserPackage\Models\Identity;

class IdentityRequest extends FormRequest
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
            'firstname' => ['nullable', 'string', 'max:225'],
            'lastname' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable','numeric', 'digits:8'],
            'birthdate' => ['nullable','date', 'date_format:Y-m-d'],
            'birth_place' => ['nullable','date'],
            'country_id' => ['nullable','exists:countries,id'],
            'photo' => [''],
            'profession_id' => ['nullable','exists:profession,id'],
            'address' => ['nullable','string', 'max:225'],
            'telephone_fix' => ['nullable','numeric', 'digits:8'],
            'telephone_professional' => ['nullable','numeric', 'digits:8'],
            'children' => ['nullable','numeric'],
            'matrimonial_status' => ['string', 'max:225'],
            'ifu' => ['nullable','string', Rule::unique('identities', 'ifu')->ignore($this->identity->id)],
            'npi' => ['required','string', Rule::unique('identities', 'npi')->ignore($this->identity->id)],
            'email' => ['nullable','email' ,Rule::unique('identities', 'email')->ignore($this->identity?->id), new UniqueLabelSlugRule(Identity::class, $this->identity?->id)],
        ];
    }
}
