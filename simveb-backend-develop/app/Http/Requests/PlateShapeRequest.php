<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlateShapeRequest extends FormRequest
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
            'name' => ['string', Rule::unique('plate_shapes', 'name')->ignore($this->route()->parameter('plate_shape'))],
            'description' => ['nullable', 'string'],
            'cost' => ['integer', 'min:0'],
            'image' => ['nullable', 'file']
        ];
    }
}
