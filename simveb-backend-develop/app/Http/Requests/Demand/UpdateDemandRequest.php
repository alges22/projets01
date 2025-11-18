<?php

namespace App\Http\Requests\Demand;

use App\Models\Config\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDemandRequest extends FormRequest
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
        $service = Service::find($this->demand->service_id);
        $rules = GetDemandRules::getDemandUpdateRuleByService($service, $this);

        return [
            ...$rules
        ];
    }
}
