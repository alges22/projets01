<?php

namespace App\Http\Requests\Plate;

use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Models\Plate\PlateOrder;
use Illuminate\Foundation\Http\FormRequest;

class ValidatePlateOrderRequest extends FormRequest
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
            'plate_order_id' => ['required', 'exists:plate_orders,id', function ($attribute, $value, $fail) {
                $plateOrder = PlateOrder::find($value);
                if ($plateOrder) {
                    if (!in_array($plateOrder->status, [Status::confirmed->name, Status::paid->name])) {
                        $fail('Cette demande ne peut pas encore être validée.');
                    }

                    if ($plateOrder->status == Status::validated->name) {
                        $fail('Cette demande est déjà traitée.');
                    }

                    $onlineProfile = getOnlineProfile();
                    if ((!$plateOrder->buyer && $onlineProfile->type->code != ProfileTypesEnum::anatt->name) || ($plateOrder->buyer && $plateOrder->buyer->id != $onlineProfile->institution_id)) {
                        $fail('Vous ne pouvez pas valider cette commande.');
                    }
                }
            }],
            'file' => ['required', 'file',]
        ];
    }
}
