<?php

namespace Ntech\UserPackage\Http\Requests\Register;

use App\Rules\Demands\ValideNpiRule;
use Illuminate\Foundation\Http\FormRequest;

class InitRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'person_type' => ['required', 'string', 'in:physical,moral'],
        ];

        if ($this->person_type == 'physical') {
            $rules += [
                'email' => ['required', 'email', 'unique:users,email',],
                'npi' => ['required', 'string', 'unique:identities,npi', 'unique:users,username', new ValideNpiRule],
            ];
        } else {
            $rules += [
                'ifu' => ['required', 'string', 'size:13', 'unique:institutions,ifu'],
            ];
        }

        return $rules;
    }
}
