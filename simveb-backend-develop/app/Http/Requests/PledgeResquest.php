<?php

namespace App\Http\Requests;

use App\Consts\Roles;
use App\Rules\Demands\ValidDemandOtpRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\FinancialInstitutionForPledgeRule;
use App\Rules\PledgeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PledgeResquest extends FormRequest
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
        $rules = [
            'vin' => [new PledgeRule],
            'pledge_file' => ['array'],
            'pledge_file.*' => ['file', 'mimes:jpeg,png,pdf'],
            'clerk' => ['uuid', 'exists:profiles,id'],
            'financial_institution' => ['uuid', 'exists:institutions,id', new FinancialInstitutionForPledgeRule],
            'rejected_reasons' => ['string'],
            'authorization_id' => ['uuid', 'exists:demand_otps,id', new ValidDemandOtpRule],
        ];

        if ($this->isMethod('post')) {
            $rules['vin'][] = 'required';
            $rules['pledge_file'][] = 'required';
            $rules['authorization_id'][] = 'required';
        }

        if ($this->isMethod('put') && $this->routeIs('pledge.reject')) {
            $rules['rejected_reasons'][] = 'required';
        }

        if ($this->routeIs('pledge.affectationToClerk')) {
            $rules['affected_to_clerk'][] = 'required';
        }

        if (getOnlineProfile()->hasRole([Roles::DISTRIBUTOR]) && $this->routeIs('pledge.store')) {
            $rules['financial_institution'][] = 'required';
        }

        return $rules;
    }
}
