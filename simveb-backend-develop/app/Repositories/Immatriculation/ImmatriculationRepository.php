<?php
namespace App\Repositories\Immatriculation;

use App\Models\Account\User;
use App\Models\GrayCard;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Vehicle\VehicleOwner;

class ImmatriculationRepository
{
    public function search()
    {
        $keyword = strtoupper((request()->get('keyword')));
        $field = request('field');
        $vehicleOwner = VehicleOwner::where('identity_id', User::find(request('user_id'))->identity_id)->first();

        if ($field == 'immatriculation') {
            $result = Immatriculation::whereHas('immatriculationDemand', function ($q) use ($vehicleOwner) {
                $q->where('vehicle_owner_id', $vehicleOwner->id);
            })
            ->where('number_label', 'like', "%$keyword%")->select(['id'])->first()?->id;
        } else {
            $grayCard = GrayCard::where('vehicle_owner_id', $vehicleOwner->id)->where('number', 'like', "%$keyword%")->first();

            $result = $grayCard ? $grayCard->immatriculation_id : null;
        }

        return ['id' => $result];
    }
}
