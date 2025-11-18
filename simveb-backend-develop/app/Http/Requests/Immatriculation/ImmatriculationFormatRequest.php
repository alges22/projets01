<?php

namespace App\Http\Requests\Immatriculation;

use App\Rules\ImmatriculationFormatRule;
use App\Rules\ProfileTypeImmatriculationFormatRule;
use App\Rules\VehicleCategoryImmatriculationFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImmatriculationFormatRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'profile_type_id' => ['nullable',Rule::requiredIf($this->isNotFilled('vehicle_category_id')),'exists:profile_types,id',new ProfileTypeImmatriculationFormatRule($this->route()->parameter('immatriculation_format')?->id)],
            'vehicle_category_id' => ['nullable',Rule::requiredIf($this->isNotFilled('profile_type_id')),'exists:vehicle_categories,id',new VehicleCategoryImmatriculationFormatRule($this->profile_type_id, $this->route()->parameter('immatriculation_format')?->id)],
            'components' => ['required','array','min:3','max:5',new ImmatriculationFormatRule],
            'components.*.id' => ['required','exists:format_components,id'],
            'components.*.value' => ['string','nullable'],
            'components.*.position' => ['required','numeric','min:1','max:5'],
        ];
    }
}
