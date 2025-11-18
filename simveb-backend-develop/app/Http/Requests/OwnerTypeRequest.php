<?php

namespace App\Http\Requests;

use App\Models\Config\OwnerType;
use App\Rules\UniqueLabelSlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OwnerTypeRequest extends FormRequest
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
        return [
            'label' =>  ['required', 'string', 'max:255', Rule::unique('owner_types', 'label')->ignore($this->owner_type?->id), new UniqueLabelSlugRule(OwnerType::class, $this->owner_type?->id)],
        ];
    }
}
