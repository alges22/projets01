<?php

namespace App\Http\Requests\Demand;

use App\Models\Config\Service;
use App\Rules\Demands\DemandAlreadyExistRule;
use App\Rules\Demands\IfuHasAccountRule;
use App\Rules\Demands\IsOwnerNpiRule;
use App\Rules\Demands\NpiHasAccountRule;
use App\Rules\Demands\ServiceIsAvailable;
use App\Rules\Demands\ServiceIsAvailableRule;
use App\Rules\Demands\ValidDemandOtpRule;
use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemandRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $service = Service::find($this->service_id);
        $rules = GetDemandRules::getDemandRuleByService($service, $this);
        $this->merge(['profile_id' => getOnlineProfile()->id]);
        $this->merge(['institution_id' => getOnlineProfile()->institution_id]);

        return [
            'npi' => ['nullable', Rule::requiredIf($this->isNotFilled('ifu')), new ValideNpiRule, new IsOwnerNpiRule, new NpiHasAccountRule],
            'ifu' => ['nullable', Rule::requiredIf($this->isNotFilled('npi')), new ValideIfuRule, new IfuHasAccountRule],
            'authorization_id' => ['nullable','uuid','exists:demand_otps,id', new ValidDemandOtpRule],
            'service_id' => ['required','exists:services,id','bail', new DemandAlreadyExistRule($this->vin), new ServiceIsAvailableRule],
            'profile_id' => ['exists:profiles,id','nullable', Rule::requiredIf($this->isNotFilled('institution_id'))],
            'institution_id' => ['nullable','exists:institutions,id', Rule::requiredIf($this->isNotFilled('profile_id')),'string'],
            ...$rules
        ];
    }
}
