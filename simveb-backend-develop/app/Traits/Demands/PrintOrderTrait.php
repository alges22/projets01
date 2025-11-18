<?php

namespace App\Traits\Demands;

use App\Consts\AvailableServiceType;
use App\Consts\NotificationNames;
use App\Enums\PrintOrderTypesEnum;
use App\Enums\Status;
use App\Models\Order\Demand;
use App\Models\Treatment\PrintOrder;

trait PrintOrderTrait
{
    public function emitPrintOrder(Demand $demand): PrintOrder|string
    {
        if ($demand->status != Status::validated->name) {
            return 'Impossible de faire cette action sur cette demande.';
        }

        if ($demand->printOrders()->where('status', Status::validated->name)->exists()) {
            return 'Cette demande possède déjà un ordre d\'impression validé.';
        }

        $demand->activeTreatment->update([
            'print_order_emitted_by' => getOnlineProfile()?->id,
            'print_order_emitted_at' => now(),
        ]);

        $demand->update(['status' => Status::print_order_emitted->name]);

        $demand->printOrders()->where('status', '!=', Status::rejected->name)->update(['status' => Status::rejected->name, 'rejected_at' => now()]);

        $orderType = getPrintOrderType($demand);

        $printOrder = PrintOrder::create([
            'demand_id' => $demand->id,
            'type' => $orderType,
            'author_id' => getOnlineProfile()->id,
            'status' => in_array($orderType, [PrintOrderTypesEnum::plate->name, PrintOrderTypesEnum::both->name]) ? Status::pending->name : Status::active->name,
            'plate_status' => in_array($orderType, [PrintOrderTypesEnum::plate->name, PrintOrderTypesEnum::both->name]) ? Status::pending->name : null,
            'card_status' => in_array($orderType, [PrintOrderTypesEnum::gray_card->name, PrintOrderTypesEnum::both->name]) ? Status::pending->name : null,
        ]);

        $serviceCode = $printOrder->demand->service->type->code;
        $demandModel = $printOrder->demand->model;

        sendMail(
            null,
            $demand->vehicle->owner->identity,
            NotificationNames::IMMATRICULATION_PRINT_ORDER_EMITTED,
            [
                'reference' => $printOrder->reference,
            ]
        );

        return $printOrder;
    }
}
