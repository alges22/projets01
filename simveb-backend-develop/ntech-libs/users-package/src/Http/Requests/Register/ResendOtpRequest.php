<?php

namespace Ntech\UserPackage\Http\Requests\Register;

use Illuminate\Foundation\Http\FormRequest;

class ResendOtpRequest extends FormRequest
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
            $rules['npi'] = ['required', 'string', 'size:10'];
        } else {
            $rules['ifu'] = ['required', 'string', 'size:13'];
        }

        return $rules;
    }
}
