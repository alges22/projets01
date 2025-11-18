<?php

namespace App\Rules;

use App\Enums\Status;
use App\Models\Pledge;
use App\Models\Vehicle\Vehicle;
use Closure;
use App\Services\VehicleService;
use Illuminate\Contracts\Validation\ValidationRule;

class PledgeRule implements ValidationRule
{
    private VehicleService $service;

    public function __construct()
    {
        $this->service = new VehicleService;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicle = (new VehicleService)->checkVehicleExists(['vin' => $value]);

        if (!$vehicle) {
            $fail("Le VIN que vous avez saisi est invalide");
            return;
        }

        $vehicle = Vehicle::with('immatriculation')->where('vin', $value)->first();

        if (!$vehicle) {
            $fail("Ce véhicule n'est pas immatriculé");
            return;
        }

        $pledge = Pledge::where('vehicle_id', $vehicle->id)
            ->whereIn('status', [Status::emitted->name, Status::institution_validated->name, Status::justice_validated->name,
                Status::anatt_validated->name, Status::institution_rejected->name, Status::justice_rejected->name, Status::anatt_rejected->name])
            ->exists();

        if ($pledge) {
            if (Pledge::where('vehicle_id', $vehicle->id)->where('is_active', true)->exists()) {
                $fail('Ce véhicule est déjà sous gage');
            } else {
                $fail('Une demande de gage de ce véhicule est déjà en cours');
            }
        }
    }
}
