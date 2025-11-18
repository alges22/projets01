<?php

namespace App\Listeners;

use App\Services\Demand\DemandHandlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DemandCreatedListener
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
        DemandHandlerService::handle($event->demand, $event->data);
    }
}
