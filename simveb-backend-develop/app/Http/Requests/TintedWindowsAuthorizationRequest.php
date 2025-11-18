<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TintedWindowsAuthorizationRequest extends FormRequest
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
            'vehicule_id' => ['required', 'uuid', Rule::unique('tinted_windows_authorizations', 'vehicule_id')->ignore($this->route()->parameter('tinted_windows_authorization'))],
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
