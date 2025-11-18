<?php

namespace App\Http\Requests\Plate;

use App\Enums\ProfileTypesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlateOrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $combinations = [];
        foreach ($this->plates as $plate) {
            $combinations[] = $plate['plate_shape_id'] . $plate['plate_color_id'];
        }

        $combinationOccurences = array_count_values($combinations);

        return [
            'plates' => ['array', 'min:1'],
            'plates.*.plate_shape_id' => ['required', 'uuid', 'exists:plate_shapes,id', function ($attribute, $value, $fail) use ($combinations, $combinationOccurences) {
                $position = (int) str_replace('.plate_shape_id', '', str_replace('plates.', '', $attribute));
                $currentCombination = $combinations[$position];

                if ($combinationOccurences[$currentCombination] > 1) {
                    $fail('Vous avez déjà choisi cette combinaison de forme et de couleur, veuillez en choisir une autre.');
                }
            }],
            'plates.*.plate_color_id' => ['required', 'uuid', 'exists:plate_colors,id'],
            'plates.*.nb' => ['required', 'numeric', 'min:1'],
            'seller_id' => ['nullable', Rule::requiredIf(getOnlineProfile()->type->code == ProfileTypesEnum::anatt->name), 'exists:spaces,id'],
        ];
    }
}
