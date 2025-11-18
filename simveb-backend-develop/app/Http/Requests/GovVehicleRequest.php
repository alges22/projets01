<?php

namespace App\Http\Requests;

use App\Enums\InstitutionTypesEnum;
use App\Models\Institution\Institution;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VINIsUsedRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GovVehicleRequest extends FormRequest
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
        $this->merge(['author_id' => getOnlineProfile()->id]);

        return [
            'owner_npi' => ['required', new ValideNpiRule],
            'vin' => ['required', new ValidVINRule, new VINIsUsedRule],
            'author_id' => ['required'],
            'customs_reference' => ['nullable','string',],
            'institution_id' => ['nullable','uuid', 'exists:institutions,id',
            function ($attribute, $value, $fail) {
                $institution = Institution::find($value);
                if ($institution?->type->name != InstitutionTypesEnum::gov_institution->name) {
                    $fail("Cette institution n'est pas affiliée à l'état.");
            }},],
        ];
    }
}
