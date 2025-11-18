<?php

namespace App\Http\Requests\Auction;

use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use Illuminate\Foundation\Http\FormRequest;

class AuctionSaleVehicleRequest extends FormRequest
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
            'vehicle_vin' => ['required', 'exists:vehicles,vin'],
            'custom_receipt_reference' => ['nullable', 'string'],
            'buyer_npi' => ['required_without:buyer_ifu', new ValideNpiRule],
            'buyer_ifu' => ['required_without:buyer_npi', new ValideIfuRule],
            'price' => ['required', 'numeric', 'min:1'],
            'divesting_file' => ['nullable', 'file'],
            'pickup_order' => ['nullable', 'file'],
        ];
    }
}
