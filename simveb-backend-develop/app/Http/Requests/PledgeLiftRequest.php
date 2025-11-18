<?php

namespace App\Http\Requests;

use App\Models\PledgeLift;
use App\Rules\Demands\ValidDemandOtpRule;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PledgeLiftRequest extends FormRequest
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
        $profile = getOnlineProfile();

        $rules = [
            'pledge_file' => ['array'],
            'pledge_file.*' => ['file', 'mimes:jpeg,png,pdf'],
            'authorization_id' => ['uuid', 'exists:demand_otps,id', new ValidDemandOtpRule],
            'rejected_reasons' => ['string'],
        ];

        if ($this->routeIs('pledge.liftPledge') && $profile->hasPermissionTo('lift-pledge')) {
            $rules['authorization_id'][] = 'required';
            $rules['pledge_file'][] = 'required';
        }

        if ($this->routeIs('pledge-lift.reject') && $profile->hasAnyPermission(['reject-pledge-lift-by-anatt', 'reject-pledge-lift-by-justice'])) {
            $rules['rejected_reasons'][] = 'required';
        }

        return $rules;
    }
}
