<?php

namespace App\Http\Requests;

use App\Models\Plate\PlateColor;
use App\Rules\UniqueLabelSlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentProviderRequest extends FormRequest
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
            'name' =>  ['nullable', 'string', 'max:255', Rule::unique('payment_providers')->ignore($this->payment_provider?->id)],
            'telephone' => ['nullable','numeric', 'digits:8', Rule::unique('payment_providers')->ignore($this->payment_provider?->id)],
            'address' => ['nullable','string', 'max:225'],
            'description' => ['string','nullable'],
            'email' => ['nullable','email', Rule::unique('payment_providers')->ignore($this->payment_provider?->id)]
        ];
    }
}
