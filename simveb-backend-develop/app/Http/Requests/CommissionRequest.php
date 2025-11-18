<?php

namespace App\Http\Requests;

use App\Models\Order\Commission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommissionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required','string',Rule::unique('commissions','name')->ignore($this->route()->parameter('commission'))],
            'amount' => ['min:0','required','numeric'],
            'percentage' => ['min:0','required','numeric'],
            'calculation_base' => ['required',Rule::in([Commission::DELIVERY_FEES,Commission::AMOUNT,Commission::TOTAL_AMOUNT])],
            'description' => ['string'],
        ];
    }
}
