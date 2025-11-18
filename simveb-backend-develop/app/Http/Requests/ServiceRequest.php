<?php

namespace App\Http\Requests;

use App\Models\Config\OwnerType;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleCharacteristic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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
            'code' => ['required','string',Rule::unique('services','code')->ignore($this->service?->id)],
            'name' => ['required','string',Rule::unique('services','name')->ignore($this->service?->id)],
            'description' => ['string','nullable'],
            'type_id'  => ['required','exists:service_types,id'],
            'duration' => ['numeric','min:0','nullable'],
            'cost' => ['numeric','min:0'],
            'procedures' => ['string','nullable'],
            'who_can_apply' => ['string','nullable'],
            'link' => ['string','nullable'],
            'color' => ['string','nullable'],
            'image' => ['file','image','nullable'],
            'status' => ['string','nullable'],
            'extract' => ['string','nullable'],
            //'is_active' => ['nullable', 'boolean'],
            'target_organization_id' => ['required_unless:parent_service_id,null','exists:organizations,id'],
            'children' => ['nullable','array',],
            'children.*' => ['exists:services,id',],
            'parent_service_id' => ['nullable','exists:services,id'],
            'documents' => ['nullable','array'],
            'documents.*' => ['exists:document_types,id'],
            'vehicle_categories' => ['nullable','array',],
            'vehicle_categories.*' =>  ['exists:vehicle_categories,id'],
            'steps' => ['nullable', 'array'],
            'steps.*.step_id' => ['required', 'exists:steps,id'],
            'steps.*.position' => ['required', 'integer', 'min:1'],
            'steps.*.duration' => ['required', 'integer', 'min:1'],
            'steps.*.process_type' => ['required', 'string', 'in:automatic,manual'],
            'steps.*' => ['distinct'],
            //'can_be_demanded' => ['nullable', 'boolean'],
            'extra_services' => ['nullable', 'array'],
            'extra_services.*' => ['required', 'exists:services,id'],
            'owner_price_variations' => ['nullable', 'array'],
            'owner_price_variations.*.model_id' => ['required', 'exists:owner_types,id'],
            'owner_price_variations.*.price' => ['required', 'min:0', 'numeric'],
            'category_price_variations' => ['nullable', 'array'],
            'category_price_variations.*.model_id' => ['required', 'exists:vehicle_categories,id'],
            'category_price_variations.*.price' => ['required', 'min:0', 'numeric'],
            'characteristic_price_variations' => ['nullable', 'array'],
            'characteristic_price_variations.*.model_id' => ['required', 'exists:vehicle_characteristics,id'],
            'characteristic_price_variations.*.price' => ['required', 'min:0', 'numeric'],
        ];
    }
}
