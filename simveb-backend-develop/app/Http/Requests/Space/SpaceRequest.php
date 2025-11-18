<?php

namespace App\Http\Requests\Space;

use App\Enums\SpaceTemplateEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpaceRequest extends FormRequest
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
            //'name' => ['required', 'string'],
            'template' => ['nullable', 'string', Rule::in(SpaceTemplateEnum::toArray())],
        ];
    }
}
