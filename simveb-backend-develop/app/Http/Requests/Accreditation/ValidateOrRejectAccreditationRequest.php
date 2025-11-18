<?php

namespace App\Http\Requests\Accreditation;

use Illuminate\Foundation\Http\FormRequest;

class ValidateOrRejectAccreditationRequest extends FormRequest
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
        $routeName = $this->route()->getName();

        if ($routeName === 'accreditation.validate') {
            $this->merge(['validator_id' => getOnlineProfile()->id]);
            $data = [
                'accreditation_id' => ['required', 'uuid', 'exists:accreditations,id'],
                'validator_id' => ['required', 'uuid', 'exists:profiles,id']
            ];
        } elseif ($routeName === 'accreditation.reject') {
            $this->merge(['rejector_id' => getOnlineProfile()->id]);
            $data = [
                'accreditation_id' => ['required', 'uuid', 'exists:accreditations,id'],
                'rejector_id' => ['required', 'uuid', 'exists:profiles,id'],
                'rejected_reason' => ['present', 'nullable', 'string'],
            ];
        }

        return $data;
    }
}
