<?php

namespace App\Http\Requests;

use App\Consts\Roles;
use App\Models\Auth\Role;
use App\Rules\CheckOppositionOnVehicleRule;
use App\Rules\Demands\ValidDemandOtpRule;
use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\OwnerHasVehicleRule;
use App\Rules\ValidNpiOrIfuRule;
use Illuminate\Foundation\Http\FormRequest;

class OppositionRequest extends FormRequest
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
            'rejected_reason' => ['string', 'max:255'],
            'reason_for_opposition' => ['uuid', 'exists:title_reasons,id'],
            'vehicles' => ['array'],
            'vehicles.*' => ['uuid', 'exists:vehicles,id', new CheckOppositionOnVehicleRule],
            'opposition_file' => ['array'],
            'opposition_file.*' => ['file'],
        ];

        if ($this->isMethod('get')) {
            $rules['npi_or_ifu'] = ['required', new ValidNpiOrIfuRule, new OwnerHasVehicleRule];
        }

        if ($this->isMethod('post')) {
            $rules['vehicles'][] = 'required';
            $rules['vehicles.*'][] = 'required';
            $rules['reason_for_opposition'][] = 'required';
            $rules['npi_or_ifu'] = ['required', new ValidNpiOrIfuRule, new OwnerHasVehicleRule];
            $rules['authorization_id'] = ['required','uuid','exists:demand_otps,id', new ValidDemandOtpRule];
            $rules['opposition_file'][] = 'required';
        }

        if (getOnlineProfile()->hasPermissionTo('lift-opposition') && $this->routeIs('opposition.lift')) {
            $rules['opposition_file'][] = 'required';
        }

        if (getOnlineProfile()->hasAnyPermission('reject-opposition-by-clerk','reject-opposition-by-judge') && $this->routeIs('oppositions.reject')) {
            $rules['rejected_reason'][] = 'required';
        }

        return $rules;
    }

}
