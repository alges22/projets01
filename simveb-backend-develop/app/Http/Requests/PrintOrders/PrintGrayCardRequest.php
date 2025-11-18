<?php

namespace App\Http\Requests\PrintOrders;

use App\Enums\PrintOrderTypesEnum;
use App\Enums\Status;
use App\Models\Order\Demand;
use Illuminate\Foundation\Http\FormRequest;

class PrintGrayCardRequest extends FormRequest
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
            'demand_id' => ['bail', 'required', 'exists:demands,id', function ($attribute, $value, $fail) {
                // $demand = Demand::find($value);

                // $printOrder = $demand->latestPrintOrder;

                // if (
                //     !$printOrder
                //     || (
                //         $printOrder->type == PrintOrderTypesEnum::plate->name ||
                //         $printOrder->type == PrintOrderTypesEnum::gray_card->name && $printOrder->status != Status::active->name ||
                //         $printOrder->type == PrintOrderTypesEnum::both->name && $printOrder->plate_status != Status::validated->name
                //     )
                // ) {
                //     $fail('Impossible de faire cette action sur ce dossier.');
                // }
            }],
        ];
    }
}
