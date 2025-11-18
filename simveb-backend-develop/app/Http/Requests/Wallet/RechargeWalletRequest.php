<?php

namespace App\Http\Requests\Wallet;

use App\Rules\Demands\PaymentRefIsValidRule;
use Illuminate\Foundation\Http\FormRequest;

class RechargeWalletRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:100',],
            'payment_reference' => ['required', 'string', 'unique:transactions,payment_reference', new PaymentRefIsValidRule($this->payment_provider_id, $this->amount)],
            'payment_provider_id' => ['nullable', 'exists:payment_providers,id']
        ];
    }
}
