<?php

namespace App\Facades;

use Sentry\Laravel\Facade;

class AssignTreatmentFacade extends Facade
{

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'AssignTreatment';
    }

}
