<?php

namespace App\Http\Requests\Demand;

use App\Consts\AvailableServiceType;
use App\Models\Config\Service;
use App\Rules\Demands\CanRequestImmatriculationRule;
use App\Rules\Demands\IfuHasAccountRule;
use App\Rules\Demands\NpiHasAccountRule;
use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\ValidMotorcycleVINRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VehicleIsCarRule;
use App\Rules\DocumentRequiredRule;
use App\Services\Immatriculation\SuggestionService;
use App\Services\VehicleService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImmatriculationRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public static function storeRules(FormRequest $request): array
    {
        return [
            'is_car' => ['nullable', 'boolean'],
            'vin' => ['required', $request->is_car || !isset($request->is_car) ? new ValidVINRule : new ValidMotorcycleVINRule, new CanRequestImmatriculationRule],
            'plate_color_id' => ['nullable', 'exists:plate_colors,id'],
            'front_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
            'label' => ['nullable','string','max:8',function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkLabelIsAvailable($value)['available']){
                    $fail('Oups! Ce label est déjà pris');
                }
            }],
            'desired_number' => ['nullable','numeric','digits:4',function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkNumberIsAvailable($value)['available']){
                    $fail('Oups! Ce numéro est déjà pris');
                }
            }],
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public static function addToCartRules(Service $service, FormRequest $request): array
    {
        $vehicle = (new VehicleService)->showVehicleByvin(['vin' => $request->vin]);
        $isCar = $vehicle['vehicle']['is_car'];

        return [
            'is_car' => ['nullable', 'boolean'],
            'vin' => ['required', $isCar ? new ValidVINRule : new ValidMotorcycleVINRule, new CanRequestImmatriculationRule],
            'plate_color_id' => ['required', 'exists:plate_colors,id'],
            'front_plate_shape_id' => [$isCar ? 'required' : 'nullable', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['required', 'exists:plate_shapes,id'],
            'label' => ['nullable',Rule::requiredIf(in_array($service->type->code,  [AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL, AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL])),'string','max:8',function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkLabelIsAvailable($value)['available']){
                    $fail('Oups! Ce label est déjà pris');
                }
            }],
            'desired_number' => ['nullable',Rule::requiredIf(in_array($service->type->code, [AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL]) ),'numeric','digits:4',function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkNumberIsAvailable($value)['available']){
                    $fail('Oups! Ce numéro est déjà pris');
                }
            }],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public static function updateRules(FormRequest $request): array
    {
        return [
            'plate_color_id' => ['nullable', 'exists:plate_colors,id'],
            'front_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
            'label' => ['nullable','string','max:8',function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkLabelIsAvailable($value)['available']){
                    $fail('Oups! Ce label est déjà pris');
                }
            }],
            'desired_number' => ['nullable','numeric','digits:4',function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkNumberIsAvailable($value)['available']){
                    $fail('Oups! Ce numéro est déjà pris');
                }
            }],
        ];
    }
}
