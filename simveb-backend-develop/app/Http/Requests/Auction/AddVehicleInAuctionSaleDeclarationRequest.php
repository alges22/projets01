<?php

namespace App\Http\Requests\Auction;

use App\Models\Vehicle\Vehicle;
use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use Illuminate\Foundation\Http\FormRequest;

class AddVehicleInAuctionSaleDeclarationRequest extends FormRequest
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
            'vehicle_vin' => ['required', 'exists:vehicles,vin', function ($attribute, $value, $fail) use ($auctionSaleDeclaration) {
                $vehicle = Vehicle::where('vin', $value)->first();

                if ($vehicle && in_array($vehicle->id, $auctionSaleDeclaration->saledVehicles()->pluck('vehicle_id')->toArray())) {
                    $fail(__('Ce véhicule est déjà dans la liste des véhicules de la déclaration.'));
                }
            }],
            'buyer_npi' => ['required_without:buyer_ifu', new ValideNpiRule],
            'buyer_ifu' => ['required_without:buyer_npi', new ValideIfuRule],
            'price' => ['required', 'numeric', 'min:1'],
        ];
    }
}
