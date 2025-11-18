<?php

namespace App\Rules\Demands;

use App\Enums\Status;
use App\Repositories\Vehicle\VehicleRepository;
use App\Services\VehicleService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class IsVehicleDepositRule implements ValidationRule
{
    public function __construct(private readonly string $vin)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicle = (new VehicleRepository)->getVehicleByVin($this->vin);

        if (!$vehicle || !$vehicle
            ->titleDeposits()
            ->where('id', $value)
            ->whereHas('vehicle', fn($query) => $query->where('vin', $this->vin))
            ->whereIn('status', [Status::validated->name, Status::active->name, Status::approved->name])
            ->exists()
        ){
            $fail("Le dépôt de titre est invalide");
        }
    }
}
