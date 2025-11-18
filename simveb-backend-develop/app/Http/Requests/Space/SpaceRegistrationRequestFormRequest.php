<?php

namespace App\Http\Requests\Space;

use App\Enums\SpaceTypesEnum;
use App\Enums\ProfileTypesEnum;
use App\Rules\Demands\ValideNpiRule;
use Illuminate\Validation\Rule;
use App\Enums\SpaceTemplateEnum;
use App\Models\Auth\ProfileType;
use Illuminate\Foundation\Http\FormRequest;

class SpaceRegistrationRequestFormRequest extends FormRequest
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
        $possibleProfileTypesCodes = $this->type == SpaceTypesEnum::state->name ? [ProfileTypesEnum::police->name] :
            [ProfileTypesEnum::company->name, ProfileTypesEnum::distributor->name, ProfileTypesEnum::bank->name, ProfileTypesEnum::affiliate->name];

        $possibleProfileTypesId = ProfileType::whereIn('code', $possibleProfileTypesCodes)->pluck('id')->toArray();

        $rules = [
            'institution_id' => ['required', 'exists:institutions,id'],
            'profile_type_id' => ['required', 'exists:profile_types,id'],
            'first_member_npi' => ['required', 'string', 'size:10', new ValideNpiRule()],
            'documents' => ['nullable', 'array',],
            'documents.*.type_id' => ['required', 'exists:document_types,id'],
            'documents.*.file' => ['required', 'file'],
            'template' => ['nullable', 'string', Rule::in(SpaceTemplateEnum::toArray())],
        ];

        return $rules;
    }
}
