<?php

namespace App\Http\Requests;

use App\Models\Config\Park;
use App\Rules\UniqueLabelSlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ParkRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('parks', 'name')->ignore($this->park?->id), new UniqueLabelSlugRule(Park::class, $this->park?->id)],
            'description' => ['string'],
            'address' => ['required', 'string'],
            'longitude' => ['required', 'numeric', 'regex:/^-?\d+(\.\d+)?$/'],
            'latitude' => ['required', 'numeric', 'regex:/^-?\d+(\.\d+)?$/'],
            'vehicle_types' => ['nullable', 'array'],
            'vehicle_types.*' => ['exists:vehicle_types,id'],
            'vehicle_categories' => ['nullable', 'array'],
            'vehicle_categories.*' => ['exists:vehicle_categories,id'],
            'towns' => ['nullable', 'array'],
            'towns.*' => ['exists:towns,id'],
            'space_id' => ['required', 'exists:spaces,id']
        ];
    }
}
