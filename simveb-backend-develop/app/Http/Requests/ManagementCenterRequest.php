<?php

namespace App\Http\Requests;

use App\Models\Config\ManagementCenter;
use App\Rules\UniqueLabelSlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManagementCenterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('management_centers', 'name')->ignore($this->management_center?->id), new UniqueLabelSlugRule(ManagementCenter::class, $this->management_center?->id)],
            'manager_title' => ['required', 'string', 'max:255'],
            'town_id' => ['required', 'exists:towns,id'],
            'state_id' => ['required', 'exists:states,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'village_id' => ['required', 'exists:villages,id'],
            'responsible_id' => ['required', 'exists:profiles,id'],
            'management_center_type_id' => ['required', 'exists:management_center_types,id'],
            'services' => ['array'],
            'services.*' => ['required', 'exists:services,id'],
            'parks' => ['nullable', 'array'],
            'parks.*' => ['exists:parks,id'],
            'zones' => ['required', 'array'],
            'zones.*' => ['exists:zones,id'],
            'staff_id' => ['nullable', 'exists:staff,id'],
        ];
    }
}
