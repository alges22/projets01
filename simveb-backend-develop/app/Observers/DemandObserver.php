<?php

namespace App\Observers;

use App\Consts\NotificationNames;
use App\Enums\PrintOrderTypesEnum;
use App\Enums\Status;
use App\Models\GrayCard;
use App\Models\Order\Demand;
use App\Repositories\Immatriculation\GrayCardRepository;
use App\Services\TreatmentTimeService;
use Illuminate\Support\Facades\Log;

class DemandObserver
{
    /**
     * Handle the Demand "created" event.
     */
    public function created(Demand $demand): void
    {
        //
    }

    /**
     * Handle the Demand "updated" event.
     */
    public function updated(Demand $demand): void
    {
        try {
            if (!$demand->originalIsEquivalent('status')) {
                switch ($demand->status) {
                    case Status::validated->name:
                    {
                        if (in_array(getPrintOrderType($demand), [PrintOrderTypesEnum::gray_card->name, PrintOrderTypesEnum::both->name])) {
                            (new GrayCardRepository(new GrayCard))->storeAfterDemandValidation($demand);
                        }
                        $certificate = $demand->refresh()->model->certificate;
                        $notifData = [
                            'attachment' => $certificate
                                ? str_replace(asset('storage'), storage_path('app/public'), $demand->model->generateCertificate(stream: false))
                                : null,
                            'reference' => $demand->reference,
                            'entityName' => $demand->model->getEntityName(),
                            'immatriculation_Number' => $demand->vehicle->immatriculation?->number_label,
                            'vin' => $demand->vehicle->vin
                        ];

                        sendNotification(NotificationNames::DEMAND_VALIDATED, $demand->vehicleOwner?->identity, data: $notifData, channels: ['mail']);
                        break;
                    }
                    case Status::rejected->name:
                    {
                        $notifData = [
                            'reference' => $demand->reference,
                            'entityName' => $demand->model->getEntityName(),
                            'vin' => $demand->vehicle->vin,
                            'reason' => $demand->activeTreatment->rejected_reason
                        ];

                        sendNotification(NotificationNames::DEMAND_REJECTED, $demand->vehicleOwner?->identity, data: $notifData, channels: ['mail']);
                        break;
                    }
                    case Status::print_order_emitted->name:
                        break;
                    case Status::print_order_validated->name:
                        break;
                    case Status::closed->name:
                        break;
                    default:
                        break;
                }

                $demand->initProfileAction();
                updateProfileActionOnDemand($demand);
                if ($demand->activeTreatment) {
                    (new TreatmentTimeService)->startTreatmentTime($demand->activeTreatment, $demand->status);
                }
            }
        } catch (\Exception $exception) {
            Log::debug($exception);
        }
    }
}
