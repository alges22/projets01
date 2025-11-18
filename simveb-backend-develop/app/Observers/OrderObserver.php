<?php

namespace App\Observers;

use App\Consts\NotificationNames;
use App\Models\Order\Order;
use App\Notifications\NotificationSender;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Notification;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if (/* !$order->originalIsEquivalent('status') &&  */$order->status == 'Approuvé'){
            sendMail(
                null,
                $order->profile->identity,
                NotificationNames::ORDER_SUBMITTED,
                [
                    'file' => (new InvoiceService)->generate($order,true),
                    'reference' => $order->reference,
                ]
            );
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

}
