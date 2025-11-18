<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
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
        $rules = [
            'payment_id' => ['required','integer',Rule::unique('transactions','payment_id')],
            'service_type' => ['required', 'in:immatriculation,plate-transformation,immatriculation-duplicate,gray-card-duplicate,prestige-label-immatriculation,sale-declaration'],
        ];

        if ($this->service_type == 'immatriculation-duplicate') {
            $rules = $rules + ['demand_id' => ['exists:plate_duplicates,id']];
        } elseif ($this->service_type == 'plate-transformation') {
            $rules = $rules + ['demand_id' => ['exists:plate_transformations,id']];
        } elseif ($this->service_type == 'gray-card-duplicate') {
            $rules = $rules + ['demand_id' => ['exists:gray_card_duplicates,id']];
        }elseif ($this->service_type == 'sale-declaration') {
            $rules = $rules + ['demand_id' => ['exists:sale_declarations,id']];
        } elseif ($this->service_type == 'immatriculation') {
            $rules = $rules + ['demand_id' => ['exists:demands,id']];
        }

        return $rules;
    }
}
