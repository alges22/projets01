<?php

namespace App\Http\Requests;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlateTransformationRequest extends FormRequest
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
            'gray_card_id' => ['required_without:immatriculation_id','exists:gray_cards,id'],
            'immatriculation_id' => ['required_without:gray_card_id','exists:immatriculations,id'],
            'comment' => ['nullable', 'string'],
            'documents' => ['nullable', 'array'],
            'service_id' => ['required','exists:services,id'],
            'payment_status' => ['required',Rule::in(Status::toArray())],
            'vin' => ['nullable'],
            'number_of_seats' => ['nullable','numeric','min:0'],
            'vehicle_category_id' => ['nullable','exists:vehicle_categories,id'],
            'engin_number' => ['nullable'],
            'energy_type_id' => ['nullable', 'exists:vehicule_energy_sources,id'],
        ];
    }
}
