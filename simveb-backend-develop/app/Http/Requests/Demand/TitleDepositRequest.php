<?php

namespace App\Http\Requests\Demand;

use App\Models\Config\Service;
use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VehicleIsImmatriculatedRule;
use App\Rules\DocumentRequiredRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TitleDepositRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function storeRules(Service $service, FormRequest $request): array
    {
        return [
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule],
            'title_reason_id' => ['required', 'uuid', Rule::exists('title_reasons', 'id')],
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function addToCartRules(Service $service, FormRequest $request): array
    {

        return [
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule],
            'title_reason_id' => ['required', 'uuid', Rule::exists('title_reasons', 'id')],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function updateRules(FormRequest $request): array
    {

        return [
            'title_reason_id' => ['nullable', 'uuid', Rule::exists('title_reasons', 'id')],
        ];
    }
}
