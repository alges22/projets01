<?php

namespace App\Http\Requests\Auth\Profile;

use App\Rules\Demands\ValideNpiRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Ntech\UserPackage\Models\Identity;

class ProfileSearchRequest extends FormRequest
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
            'profile_id' => [
                'nullable',
                'exists:profiles,id',
                Rule::requiredIf($this->isNotFilled('npi')),
                'prohibits:npi',
            ],
            'npi' => [
                'nullable',
                new ValideNpiRule,
                'exists:identities,npi',
                Rule::requiredIf($this->isNotFilled('profile_id')),
                'prohibits:profile_id',
            ],
        ];
    }
}
