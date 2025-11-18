<?php

namespace App\Http\Requests;

use App\Models\Plate\PlateColor;
use App\Rules\UniqueLabelSlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlateColorRequest extends FormRequest
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
            'label' =>  ['required', 'string', 'max:255', Rule::unique('plate_colors', 'label')->ignore($this->plate_color?->id), new UniqueLabelSlugRule(PlateColor::class, $this->plate_color?->id)],
            'color_code' => ['required', 'string', 'min:4', 'max:9', 'regex:/^#.*$/'],
            'text_color' => ['required', 'string', 'min:4', 'max:9', 'regex:/^#.*$/'],
            'cost' => ['nullable', 'integer'],
        ];
    }
}
