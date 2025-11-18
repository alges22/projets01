<?php

namespace App\Http\Requests\Demand;

use App\Models\Config\ReimmatriculationReason;
use App\Rules\Demands\CanRequestImmatriculationRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\ValidateReimmatriculationVehicle;
use App\Services\Immatriculation\SuggestionService;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

class ReimmatriculationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public static function storeRules(FormRequest $request): array
    {
        $reason = ReimmatriculationReason::find($request->reason_id);
        $vin = $request->vin;
        request()->merge(['with_immatriculation' => boolval($request->with_immatriculation)]);

        $rules = [
            'vin' => ['required', new ValidVINRule, new CanRequestImmatriculationRule(false), new ValidateReimmatriculationVehicle(reason: $reason, vin: $vin)],
            'reason_id' => ['required', 'exists:reimmatriculation_reasons,id'],
            'custom_reason' => ['nullable', 'string', 'max:255'],
            'with_immatriculation' => ['nullable', 'boolean'],

            'plate_color_id' => ['nullable', 'exists:plate_colors,id'],
            'front_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
            'label' => ['nullable', 'string', 'max:8', function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkLabelIsAvailable($value)['available']) {
                    $fail('Oups! Ce label est déjà pris');
                }
            }],
            'desired_number' => ['nullable', 'numeric', 'digits:4', function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkNumberIsAvailable($value)['available']) {
                    $fail('Oups! Ce numéro est déjà pris');
                }
            }],
        ];

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public static function addToCartRules(FormRequest $request): array
    {
        $reason = ReimmatriculationReason::find($request->reason_id);
        $vin = $request->vin;
        request()->merge(['with_immatriculation' => boolval($request->with_immatriculation)]);

        $rules = [
            'vin' => ['required', new ValidVINRule, new CanRequestImmatriculationRule(false), new ValidateReimmatriculationVehicle(reason: $reason, vin: $vin)],
            'reason_id' => ['required', 'exists:reimmatriculation_reasons,id'],
            'custom_reason' => ['nullable', new RequiredIf($reason && $reason->requires_reason), 'string', 'max:255'],
            'with_immatriculation' => ['required', 'boolean'],
            'plate_color_id' => ['required', 'exists:plate_colors,id'],
            'front_plate_shape_id' => ['required', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['required', 'exists:plate_shapes,id'],
            'label' => ['nullable', 'string', 'max:8', function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkLabelIsAvailable($value)['available']) {
                    $fail('Oups! Ce label est déjà pris');
                }
            }],
            'desired_number' => ['nullable', 'numeric', 'digits:4', function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkNumberIsAvailable($value)['available']) {
                    $fail('Oups! Ce numéro est déjà pris');
                }
            }],
        ];

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public static function updateRules(FormRequest $request): array
    {
        return [
            'plate_color_id' => ['required', 'exists:plate_colors,id'],
            'front_plate_shape_id' => ['required', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['required', 'exists:plate_shapes,id'],
            'label' => ['nullable', 'string', 'max:8', function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkLabelIsAvailable($value)['available']) {
                    $fail('Oups! Ce label est déjà pris');
                }
            }],
            'desired_number' => ['nullable', 'numeric', 'digits:4', function (string $attribute, mixed $value, Closure $fail) {
                if (!(new SuggestionService)->checkNumberIsAvailable($value)['available']) {
                    $fail('Oups! Ce numéro est déjà pris');
                }
            }],
        ];
    }
}
