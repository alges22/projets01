<?php

namespace App\Http\Requests\Reform;

use App\Enums\ProfileTypesEnum;
use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\VehicleHasTitleDepositRule;
use Illuminate\Foundation\Http\FormRequest;

class ReformDeclarationRequest extends FormRequest
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
            'auction_sale_declaration_id' => ['required', 'exists:auction_sale_declarations,id'],
            'report' => ['required', 'file'],
            'auction_vehicles' => ['required', 'array','min:1'],
            'auction_vehicles.*.id' => ['required', 'exists:auction_sale_vehicles,id', new  VehicleHasTitleDepositRule],
            'auction_vehicles.*.divesting_file' => ['required', 'file'],
            'auction_vehicles.*.pickup_order' => ['required', 'file'],
            'auction_vehicles.*.custom_receipt_reference' => ['required', 'string'],
        ];
    }
}
