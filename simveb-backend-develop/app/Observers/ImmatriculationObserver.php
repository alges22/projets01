<?php

namespace App\Observers;

use App\Enums\ReasonEnum;
use App\Models\Immatriculation\Immatriculation;
use App\Models\OwnerHistory;

class ImmatriculationObserver
{
    /**
     * Handle the Immatriculation "created" event.
     */
    public function created(Immatriculation $immatriculation): void
    {
        OwnerHistory::create([
            'vehicle_id' => $immatriculation->vehicle_id,
            'vehicle_owner_id' => $immatriculation->vehicle_owner_id,
            'reason' => ReasonEnum::immatriculated->name
        ]);
    }

    /**
     * Handle the Immatriculation "updated" event.
     */
    public function updated(Immatriculation $immatriculation): void
    {
        // if (!$immatriculation->grayCard) {
        // }
    }

    /**
     * Handle the Immatriculation "deleted" event.
     */
    public function deleted(Immatriculation $immatriculation): void
    {
        //
    }

    /**
     * Handle the Immatriculation "restored" event.
     */
    public function restored(Immatriculation $immatriculation): void
    {
        //
    }
}
