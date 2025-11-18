<?php

namespace App\Listeners;

use App\Consts\NotificationNames;
use App\Notifications\NotificationSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NewDemandSubmittedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        /*$demand = $event->demand;

        $owner = $demand->vehicleOwner;
        $verificationData = [
            'npi' => $owner->identity->npi,
            'vin' => $demand->vehicle->vin,
        ];

        if (shouldBlackList($verificationData)){
            $demand->update([
                'black_listed_at' => now(),
                'black_list_verified_at' => now()
                ]);
        }else{
            $demand->update([
                'black_list_verified_at' => now()
            ]);
        }*/

       // sendMail(null, $demand->vehicleOwner->identity, NotificationNames::IMMATRICULATION_DEMAND_SUBMITTED, ['reference' => $demand->reference]);

    }
}
