<?php

namespace App\Http\Requests;

use App\Models\Config\Service;
use App\Rules\Demands\CanRequestImmatriculationRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VehicleIsImmatriculatedRule;
use Illuminate\Foundation\Http\FormRequest;

class GlassEngravingRequest extends FormRequest
{
    /**
     * @param Service $service
     * @param FormRequest $request
     * @return array[]
     */
    public static function storeRules(Service $service, FormRequest $request): array
    {
        return [
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule, new CanRequestImmatriculationRule(false)],
        ];
    }

    /**
     * @param Service $service
     * @param FormRequest $request
     * @return array[]
     */
    public static function addToCartRules(Service $service, FormRequest $request): array
    {

        return [
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule, new CanRequestImmatriculationRule(false)],
        ];
    }
}
