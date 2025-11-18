<?php

namespace App\Http\Requests\PoliceOfficerAssignment;

use Illuminate\Foundation\Http\FormRequest;

class ValidateOrRejectPoliceOfficerAssignmentRequest extends FormRequest
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

        if ($routeName === 'officer.assignment.validate') {
            $this->merge(['validator_id' => getOnlineProfile()->id]);
            $data = [
                'assignment_id' => ['required', 'uuid', 'exists:police_officer_assignments,id'],
                'validator_id' => ['required', 'uuid', 'exists:profiles,id'],
            ];
        } elseif ($routeName === 'officer.assignment.reject') {
            $this->merge(['rejector_id' => getOnlineProfile()->id]);
            $data = [
                'assignment_id' => ['required', 'uuid', 'exists:police_officer_assignments,id'],
                'rejector_id' => ['required', 'uuid', 'exists:profiles,id'],
                'reject_reason' => ['present', 'nullable', 'string'],
            ];
        }

        return $data;
    }
}
