<?php

namespace App\Http\Requests\Demand;

use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VehicleIsImmatriculatedRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleDeclarationRequest extends FormRequest
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
    public static function storeRules(FormRequest $request): array
    {
        return [
            'new_owner_npi' => ['nullable',Rule::requiredIf($request->isNotFilled('new_owner_ifu')), new ValideNpiRule],
            'new_owner_ifu' => ['nullable',Rule::requiredIf($request->isNotFilled('new_owner_npi')), new ValideIfuRule],
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule],
            'comment' => ['nullable', 'string'],
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function addToCartRules(FormRequest $request): array
    {

        return [
            'new_owner_npi' => ['nullable',Rule::requiredIf($request->isNotFilled('new_owner_ifu')), new ValideNpiRule],
            'new_owner_ifu' => ['nullable',Rule::requiredIf($request->isNotFilled('new_owner_npi')), new ValideIfuRule],
            'vin' => ['required',new VehicleIsImmatriculatedRule],
            'comment' => ['nullable', 'string'],
        ];
    }
}
