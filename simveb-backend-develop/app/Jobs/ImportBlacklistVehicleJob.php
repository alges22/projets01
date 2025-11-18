<?php

namespace App\Jobs;

use App\Events\BlacklistDataImported;
use App\Imports\BlacklistVehicleImport;
use App\Models\Auth\Profile;
use App\Services\HandleExcelErrorsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportBlacklistVehicleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $filepath, private readonly Profile $authorProfile)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $import = new BlacklistVehicleImport($this->authorProfile);
        $import->import(public_path($this->filepath));

        $hasFailure = isset($import->failures()[0]);

        $failureFilePath = $hasFailure ? (new HandleExcelErrorsService)->HandleExcelErrors($import, public_path($this->filepath), 'blacklist_vehicles') : '';

        event(new BlacklistDataImported(
            $this->authorProfile,
            $hasFailure,
            $failureFilePath
        ));
    }
}
