<?php

namespace App\Http\Requests\Auction;

use Illuminate\Foundation\Http\FormRequest;

class RemoveVehicleFromAuctionSaleDeclarationRequest extends FormRequest
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
        $auctionSaleDeclaration = $this->auction_sale_declaration;

        return [
            'vehicle_id' => ['required', 'exists:vehicles,id', function ($attribute, $value, $fail) use ($auctionSaleDeclaration) {
                if (!in_array($value, $auctionSaleDeclaration->saledVehicles()->pluck('vehicle_id')->toArray())) {
                    $fail(__('Ce véhicule ne fait pas partie de cette vente aux enchères.'));
                }
            }]
        ];
    }
}
