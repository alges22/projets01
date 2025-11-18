<?php

namespace App\Http\Requests\Demand;

use App\Consts\AvailableServiceType;
use App\Models\Config\Service;
use App\Rules\AddCharacteristicsRule;
use App\Rules\CheckTransformationExistRule;
use App\Rules\CheckVehicleCharacteristicRule;
use App\Rules\CompareCategoriesByCharacteristicsRule;
use App\Rules\Demands\CanRequestImmatriculationRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\TintedAuthorizationRule;
use App\Services\Immatriculation\SuggestionService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleTransformationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public static function storeRules(Service $service, FormRequest $request): array
    {
        return [
            'vin' => ['required', new ValidVINRule, new CanRequestImmatriculationRule(false)],
            'value_id.*' => ['required', 'uuid', 'exists:vehicle_characteristics,id', new CompareCategoriesByCharacteristicsRule, new TintedAuthorizationRule],
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
            'vin' => ['required', new ValidVINRule, new CanRequestImmatriculationRule(false)],
            'value_id' => ['required', 'array'],
            'value_id.*' => ['uuid', 'exists:vehicle_characteristics,id', new CheckTransformationExistRule, new TintedAuthorizationRule],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public static function updateRules(FormRequest $request): array
    {
        if ($request->has(['transformation_characteristic_id', 'value_id'])) {
            return [
                'transformation_characteristic_id' => ['required', 'uuid', 'exists:transformation_characteristics,id'],
                'value_id' => ['required', 'uuid', 'exists:vehicle_characteristics,id', new CheckVehicleCharacteristicRule],
            ];
        }
        return [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function addCharacteristics(FormRequest $request): array
    {
        return [
            'transformation_id' => ['required', 'uuid', 'exists:vehicle_transformations,id'],
            'value_id' => ['required', 'array'],
            'value_id.*' => ['uuid', 'exists:vehicle_characteristics,id', new AddCharacteristicsRule, new TintedAuthorizationRule],
        ];
    }
}

