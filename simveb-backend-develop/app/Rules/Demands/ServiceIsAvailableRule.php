<?php

namespace App\Rules\Demands;

use App\Consts\AvailableServiceType;
use App\Enums\Status;
use App\Models\Config\Service;
use App\Models\Order\Demand;
use App\Models\Vehicle\Vehicle;
use App\Repositories\Vehicle\VehicleRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ServiceIsAvailableRule implements ValidationRule
{


    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = Service::query()->where('id', $value)->first();

        if(!$service->is_active || !$service->can_be_demanded){
            $fail("Le service que vous essayez de demander n'est pas disponible");
        }
    }
}
