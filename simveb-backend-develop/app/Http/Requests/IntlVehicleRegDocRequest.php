<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IntlVehicleRegDocRequest extends FormRequest
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
            'vehicle_id' => ['required', 'uuid', Rule::unique('international_vehicle_registration_documents', 'vehicle_id')->ignore($this->route()->parameter('international_vehicle_registration_document'))],
            'user_id' => ['required', 'uuid'],
            'under_review' => ['nullable', 'boolean'],
            'approved' => ['nullable', 'boolean'],
            'paid' => ['nullable', 'boolean'],
            'ongoing_issuance' => ['nullable', 'boolean'],
            'issued' => ['nullable', 'boolean'],
            'expired' => ['nullable', 'boolean'],
            'reviewed_at' => ['nullable', 'date'],
            'approved_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'issued_at' => ['nullable', 'date'],
            'expired_at' => ['nullable', 'date'],
            'validity_period_in_months' => ['required', 'integer']
        ];
    }
}
