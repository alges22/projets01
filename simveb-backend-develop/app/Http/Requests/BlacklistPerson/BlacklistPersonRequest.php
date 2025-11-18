<?php

namespace App\Http\Requests\BlacklistPerson;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlacklistPersonRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'ifu' => ['nullable', 'string', Rule::unique('blacklist_persons', 'ifu')->ignore($this->blacklist_person?->id), 'required_without_all:npi,plate_number,vin,id_number'],
            'npi' => ['nullable', 'string', Rule::unique('blacklist_persons', 'npi')->ignore($this->blacklist_person?->id), 'required_without_all:ifu,plate_number,vin,id_number'],
            'plate_number' => ['nullable', 'string', Rule::unique('blacklist_persons', 'plate_number')->ignore($this->blacklist_person?->id), 'required_without_all:ifu,npi,vin,id_number'],
            'vin' => ['nullable', 'string', Rule::unique('blacklist_persons', 'vin')->ignore($this->blacklist_person?->id), 'required_without_all:ifu,npi,plate_number,id_number'],
            'id_number' => ['nullable', 'string', Rule::unique('blacklist_persons', 'id_number')->ignore($this->blacklist_person?->id), 'required_without_all:ifu,npi,plate_number,vin'],
        ];
    }
}
