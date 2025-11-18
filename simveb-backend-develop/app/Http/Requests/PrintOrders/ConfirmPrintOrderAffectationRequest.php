<?php

namespace App\Http\Requests\PrintOrders;

use App\Enums\PrintOrderTypesEnum;
use App\Enums\Status;
use App\Models\DemandOtp;
use App\Models\Treatment\PrintOrder;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmPrintOrderAffectationRequest extends FormRequest
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
            'print_order_id' => ['bail', 'required', 'exists:print_orders,id', function ($attribute, $value, $fail) {
                // $printOrder = PrintOrder::find($value);
                // if ($printOrder->type == PrintOrderTypesEnum::gray_card->name || $printOrder->status != Status::pending->name) {
                //     $fail('Impossible de faire cette action sur cet ordre d\'impression.');
                // }
            }],
            'authorization_id' => ['bail', 'required', 'exists:demand_otps,id', function ($attribute, $value, $fail) {
                $authorization = DemandOtp::find($value);

                if ($authorization && !$authorization->is_verified) {
                    $fail('Votre autorisation n\'est pas validée.');
                }
            }],
        ];
    }
}
