<?php

namespace App\Http\Requests\Demand;

use App\Models\Config\Service;
use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VehicleIsImmatriculatedRule;
use App\Rules\DocumentRequiredRule;
use App\Services\Immatriculation\SuggestionService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImmatriculationLabelRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public static function storeRules(Service $service, FormRequest $request): array
    {

        return [
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule],
            'plate_color_id' => ['nullable', 'exists:plate_colors,id'],
            'front_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
            'label' => ['nullable','string','max:8',function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkLabelIsAvailable($value)['available']){
                    $fail('Oups! Ce label est déjà pris');
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

        return [
            'vin' => ['required', new ValidVINRule,  new VehicleIsImmatriculatedRule],
            'plate_color_id' => ['required', 'exists:plate_colors,id'],
            'front_plate_shape_id' => ['required', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['required', 'exists:plate_shapes,id'],
            'label' => ['required','string','max:8',function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkLabelIsAvailable($value)['available']){
                    $fail('Oups! Ce label est déjà pris');
                }
            }],
        ];
    }

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
        ];
    }
}
