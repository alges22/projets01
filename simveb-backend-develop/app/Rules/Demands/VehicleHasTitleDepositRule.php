<?php

namespace App\Rules\Demands;

use App\Enums\Status;
use App\Models\Auction\AuctionSaleVehicle;
use App\Models\Order\Demand;
use App\Models\Title\TitleDeposit;
use App\Models\Vehicle\Vehicle;
use App\Repositories\Vehicle\VehicleRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class VehicleHasTitleDepositRule implements ValidationRule
{
    public function __construct()
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicleRepository = new VehicleRepository;
        $auctionVehicle = AuctionSaleVehicle::find($value);
        $vehicle = $vehicleRepository->find($auctionVehicle?->vehicle_id);

        $titleDeposit = $vehicle?->titleDeposits()
            ->where('status', Status::validated->name)
            ->first();

        if(!$titleDeposit){
            $fail('Aucun dépot de titre n\'est en actif pour ce véhicule');
        }
    }
}
