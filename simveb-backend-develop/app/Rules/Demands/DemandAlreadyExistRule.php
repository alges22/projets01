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

class DemandAlreadyExistRule implements ValidationRule
{
    public function __construct(private ?string $vin, private ?string $skipId = null, private $isAddingToCart = false)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicleRepository = new VehicleRepository;
        $vehicle = $vehicleRepository->findWhere(['vin' => $this->vin]);

        $transformationService = Service::query()->where('id', $value)
            ->where('code', AvailableServiceType::VEHICLE_TRANSFORMATION)
            ->exists();

        $demandQuery = Demand::query()
            ->where('service_id', $value)
            ->where('vehicle_id', $vehicle?->id)
            ->when($this->skipId, fn($query) => $query->where('id','!=',$this->skipId));

        if (!$transformationService){
            $demandQuery->where('status','!=', Status::pending->name);
        }else{
            $demandQuery->whereIn('status', $this->isAddingToCart
                ? [Status::in_cart->name]
                : [Status::in_cart->name, Status::pending->name]
            );

        }

        $demand = $demandQuery->first();

        if($demand != null){
            $fail('Une demande pour ce service est déjà en cours ou déjà faite');
        }
    }
}
