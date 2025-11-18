<?php

namespace App\Http\Requests\ImpressionDemand;

use App\Models\ImpressionDemand;
use App\Models\Plate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateImpressionDemandRequest extends FormRequest
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
    public function rules(): array
    {
        $impressionDemandId = $this->impression_demand_id;
        return [
            'impression_demand_id' => ['required', 'exists:impression_demands,id'],
            'front_plate_id' => ['required', 'exists:plates,id', function ($attribute, $value, $fail) {
                $plate = Plate::findOrFail($value);

                if (!$plate->in_space_stock) {
                    $fail("La plaque $attribute n'est plus en stock");
                }
            }],
            'back_plate_id' => ['nullable', 'different:front_plate_id', 'exists:plates,id', Rule::requiredIf(function () use ($impressionDemandId) {
                if (!empty($impressionDemandId)) {
                    $impressionDemand = ImpressionDemand::findOrFail($impressionDemandId);

                    return $impressionDemand->demand->back_plate_shape_id;
                }
                return false;
            }), function ($attribute, $value, $fail) {
                $plate = Plate::findOrFail($value);

                if (!$plate->in_space_stock) {
                    $fail("La plaque $attribute n'est plus en stock");
                }
            }],
        ];
    }
}
