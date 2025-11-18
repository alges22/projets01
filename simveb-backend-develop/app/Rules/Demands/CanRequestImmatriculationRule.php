<?php

namespace App\Rules\Demands;

use App\Enums\Status;
use App\Repositories\Vehicle\VehicleRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CanRequestImmatriculationRule implements ValidationRule
{
    private bool $doesNotHaveImmatriculation;

    public function __construct($doesNotHaveImmatriculation = true)
    {
        $this->doesNotHaveImmatriculation = $doesNotHaveImmatriculation;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicle = (new VehicleRepository)->getVehicleByVin($value);

        if ($this->doesNotHaveImmatriculation) {
            if ($vehicle?->immatriculation && in_array($vehicle?->immatriculation?->status, [Status::validated->name, Status::print_order_emitted->name, Status::print_order_validated->name, Status::closed->name])) {
                $fail('Impossible d\'éffectuer cette demande, car le véhicule est déjà immatriculé ou a un processus d\'immatriculation en cours.');
            }
        } else {
            if ($vehicle?->immatriculation && !in_array($vehicle?->immatriculation?->demand?->status, [Status::closed->name])) {
                $fail('Impossible d\'éffectuer cette demande, car ce véhicule n\'est pas immatriculé.');
            }
        }
    }
}
