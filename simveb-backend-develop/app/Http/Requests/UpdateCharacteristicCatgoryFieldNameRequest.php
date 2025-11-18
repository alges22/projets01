<?php

namespace App\Http\Requests;

use App\Services\CharacteristicCategoryService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCharacteristicCatgoryFieldNameRequest extends FormRequest
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
        $fields = (new CharacteristicCategoryService)->fetchCharacteristicFields();

        return [
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.category_id' => ['required', 'exists:vehicle_characteristic_categories,id'],
            'fields.*.field_name' => ['required', 'string', Rule::in($fields)], //check unicity
        ];
    }
}
