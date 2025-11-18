<?php

namespace App\Repositories\Vehicle;

use App\Models\Vehicle\VehiclePassage;
use App\Repositories\Crud\AbstractCrudRepository;
use Illuminate\Database\Eloquent\Builder;

class VehiclePassageHistoryRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(VehiclePassage::class);
    }

    /**
     *
     */
    public function allPassagesOfVehicle(string $immatriculationNumber)
    {
        if ($vehicle = getVehicleByImmatriculation($immatriculationNumber)) {
            return VehiclePassage::where('vehicle_id', $vehicle->id)->orderByDesc('created_at')->filter()->paginate();
        }

        return VehiclePassage::where('foreign_vehicle_immatriculation_number', $immatriculationNumber)->orderByDesc('created_at')->filter()->paginate();
    }

    /**
     *
     */
    public function historyOfPassage(VehiclePassage $passage)
    {
        $passageHistory = VehiclePassage::query()
            ->whereNot('id', $passage->id)
            ->where(function (Builder $query) use ($passage) {
                $query->where('vehicle_id', $passage->vehicle_id)
                    ->where('foreign_vehicle_immatriculation_number', $passage->foreign_vehicle_immatriculation_number);
            })
            ->latest()
            ->filter()
            ->paginate();

        return $passageHistory;
    }
}
