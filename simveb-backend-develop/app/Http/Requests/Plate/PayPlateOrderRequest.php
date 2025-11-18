<?php

namespace App\Http\Requests\Plate;

use App\Enums\Status;
use App\Models\Plate\PlateOrder;
use Illuminate\Foundation\Http\FormRequest;

class PayPlateOrderRequest extends FormRequest
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
        $plateOrder = PlateOrder::find($this->plate_order_id);

        return [
            'plate_order_id' => ['required', 'exists:plate_orders,id', function ($attribute, $value, $fail) use ($plateOrder) {
                if ($plateOrder) {
                    if ($plateOrder->status != Status::waiting_for_payment->name) {
                        $fail("Impossible de faire cette action sur cette commande.");
                    }

                    $walletBalance = getOnlineProfile()->space?->wallet?->balance ?? 0;

                    if ($walletBalance < $plateOrder->amount) {
                        $fail(__('Le solde de votre portefeuille est insuffisant, veuillez le recharger.'));
                    }
                }
            }],
        ];
    }
}
