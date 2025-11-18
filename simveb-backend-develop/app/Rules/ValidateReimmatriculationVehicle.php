<?php

namespace App\Rules;

use App\Models\Config\ReimmatriculationReason;
use App\Models\Vehicle\Vehicle;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateReimmatriculationVehicle implements ValidationRule
{
    public function __construct(private readonly ReimmatriculationReason|null $reason, private readonly string|null $vin)
    {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->reason) {
            $vehicle = Vehicle::where('vin', $value)->first();

            if ($vehicle) {
                if ($this->reason->requires_title_deposit && !$vehicle->titleDeposits()->exists()) {
                    $fail(__('Ce véhicule n\'a pas de dépot de titre'));
                }
                if ($this->reason->requires_transfer_certificate && !$vehicle->titleDeposits()->exists()) {
                    $fail(__('Ce véhicule n\'a pas de certificat de cession'));
                }
            }
        }
    }
}
