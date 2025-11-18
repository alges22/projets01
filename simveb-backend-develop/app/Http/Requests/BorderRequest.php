<?php

namespace App\Http\Requests;

use App\Models\Config\Border;
use App\Rules\UniqueLabelSlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BorderRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('borders', 'name')->ignore($this->border?->id), new UniqueLabelSlugRule(Border::class, $this->border?->id)],
            'border_country_id' => ['required', 'exists:countries,id'],
            'town_id' => ['exists:towns,id', Rule::unique('borders', 'town_id')->ignore($this->border?->id)],
            'longitude' => ['required', 'numeric', 'regex:/^-?\d+(\.\d+)?$/'],
            'latitude' => ['required', 'numeric', 'regex:/^-?\d+(\.\d+)?$/'],
        ];
    }
}
