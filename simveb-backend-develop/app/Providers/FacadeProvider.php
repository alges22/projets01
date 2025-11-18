<?php

namespace App\Providers;

use App\Services\Treatment\AssignTreatmentService;
use App\Services\Treatment\TreatmentService;
use App\Services\Treatment\ValidateTreatmentService;
use Illuminate\Support\ServiceProvider;

class FacadeProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('AssignTreatment',function (){
            return new AssignTreatmentService;
        });

        $this->app->bind('ValidateTreatment',function (){
            return new ValidateTreatmentService;
        });

        $this->app->bind('Treatment',function (){
            return new TreatmentService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
