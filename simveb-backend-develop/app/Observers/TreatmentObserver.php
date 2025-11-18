<?php

namespace App\Observers;


use App\Models\Treatment\Treatment;
use App\Services\TreatmentTimeService;
use Illuminate\Support\Facades\Log;

class TreatmentObserver
{
    public function __construct(private readonly TreatmentTimeService $treatmentTimeService)
    {}

    /**
     * Handle the Treatment "created" event.
     * @throws \Exception
     */
    public function created(Treatment $treatment): void
    {
    }

    /**
     * Handle the Treatment "updated" event.
     * @throws \Exception
     */
    public function updated(Treatment $treatment): void
    {

    }

}
