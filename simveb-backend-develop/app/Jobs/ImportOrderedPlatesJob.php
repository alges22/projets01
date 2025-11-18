<?php

namespace App\Jobs;

use App\Events\PlatesImported;
use App\Imports\InstitutionPlatesImport;
use App\Imports\OrderedPlatesImport;
use App\Models\Plate\PlateOrder;
use App\Services\HandleExcelErrorsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportOrderedPlatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $filepath, private readonly PlateOrder $plateOrder)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $platesImport = new InstitutionPlatesImport($this->plateOrder);

        $platesImport->import(public_path($this->filepath));

        $hasFailure = isset($platesImport->failures()[0]);

        $failureFilePath = $hasFailure ? (new HandleExcelErrorsService)->HandleExcelErrors($platesImport, public_path($this->filepath), 'ordered_plates') : '';

        event(new PlatesImported(
            $this->plateOrder->validator,
            $hasFailure,
            $failureFilePath
        ));
    }
}
