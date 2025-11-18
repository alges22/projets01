<?php

namespace App\Http\Requests\Demand;

use App\Models\Config\Service;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VehicleIsImmatriculatedRule;
use Illuminate\Foundation\Http\FormRequest;

class TintedWindowAuthorizationRequest extends FormRequest
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
        ];
    }
}
