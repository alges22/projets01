<?php

namespace App\Http\Requests;

use App\Models\Order\Order;
use App\Rules\Demands\PaymentRefIsValidRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $order = Order::find($this->order_id);
        return [
            'payment_reference' => ['required','string',Rule::unique('transactions','payment_reference'), new PaymentRefIsValidRule($this->payment_provider_id, $order?->amount)],
            'order_id' => ['required','bail','exists:orders,id', function ($attribute, $value, $fails) {
                $order = Order::query()->find($value);
                if ($order->demands()->count() == 0){
                    $fails("Aucune demande n'est dans la commande. Assurez-vous d'avoir validé le panier.");
                }
            }],
            'payment_provider_id' => ['nullable', 'exists:payment_providers,id']
        ];
    }
}
