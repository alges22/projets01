<?php

namespace Ntech\UserPackage\Http\Requests\Register;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
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
                'npi' => ['required', 'string', 'size:10', 'unique:identities,npi', 'unique:users,username',],
                'state_id' => ['required', 'exists:states,id'],
                'town_id' => ['required', 'exists:towns,id'],
                'district_id' => ['required', 'exists:districts,id'],
                'village_id' => ['required', 'exists:villages,id'],
                'house' => ['required', 'string'],
            ];
        } else {
            $rules += [
                'ifu' => ['required', 'string', 'size:13', 'unique:institutions,ifu'],
                'first_member_npi' => ['required', 'string', 'size:10'],
                //TODO: add validation rules for required document types
                'documents' => ['nullable', 'array',],
                'documents.*.type_id' => ['required', 'exists:document_types,id'],
                'documents.*.file' => ['required', 'file'],
                //TODO: register town_id, district_id and village of main location
            ];
        }

        return $rules;
    }
}
