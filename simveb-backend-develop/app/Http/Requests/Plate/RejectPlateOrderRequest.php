<?php

namespace App\Http\Requests\Plate;

use App\Models\Plate\PlateOrder;
use App\Rules\CanConfirmPlateOrderRule;
use Illuminate\Foundation\Http\FormRequest;

class RejectPlateOrderRequest extends FormRequest
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
        $plateOrder = PlateOrder::find($this->plate_order_id);

        return [
            'plate_order_id' => ['required', 'exists:plate_orders,id', new CanConfirmPlateOrderRule($plateOrder)],
            'reason' => ['required', 'string'],
        ];
    }
}
