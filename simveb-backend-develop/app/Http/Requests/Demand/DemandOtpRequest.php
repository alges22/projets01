<?php

namespace App\Http\Requests\Demand;

use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemandOtpRequest extends FormRequest
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
        return [
            'owner_npi' => [Rule::requiredIf($this->isNotFilled('owner_ifu')),'string','digits:10', new ValideNpiRule()],
            'owner_ifu' => [Rule::requiredIf($this->isNotFilled('owner_npi')),'string','digits:13', new ValideIfuRule()],
            'buyer_npi' => ['sometimes','string','digits:10', new ValideNpiRule()],
            'buyer_ifu' => ['sometimes','string','digits:13', new ValideIfuRule()],
        ];
    }
}
