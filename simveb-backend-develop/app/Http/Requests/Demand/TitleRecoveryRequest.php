<?php

namespace App\Http\Requests\Demand;

use App\Models\Config\Service;
use App\Models\Title\TitleDeposit;
use App\Rules\Demands\IsVehicleDepositRule;
use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VehicleIsImmatriculatedRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TitleRecoveryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public static function storeRules(Service $service, FormRequest $request): array
    {
        if ($request->isNotFilled('deposit_id')){
            $titleDeposit = TitleDeposit::query()
                ->whereRelation('vehicle','vin', $request->vin)
                ->latest()
                ->first();
            request()->merge(['deposit_id' => $titleDeposit?->id]);
        }

        return [
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule],
            'comment' => ['nullable','string'],
            'deposit_id' => ['required', 'uuid', Rule::exists('title_deposits', 'id'),  new IsVehicleDepositRule($request->vin)],
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public static function addToCartRules(Service $service, FormRequest $request): array
    {

        return [
            'vin' => ['required', 'bail', new ValidVINRule, new VehicleIsImmatriculatedRule],
            'comment' => ['nullable','string'],
            'deposit_id' => ['required', 'uuid', Rule::exists('title_deposits', 'id'), new IsVehicleDepositRule($request->vin)],
        ];
    }
}
