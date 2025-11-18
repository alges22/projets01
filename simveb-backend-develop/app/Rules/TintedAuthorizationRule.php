<?php

namespace App\Rules;

use App\Models\TintedWindowAuthorization;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Enums\GlassTypeEnum;
use App\Models\VehicleTransformation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TintedAuthorizationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $characteristics = request()->input('value_id', []);

        if (request()->vin) {
            $vehicle = Vehicle::where('vin', request()->vin)->pluck('id')->first();
        }else{
            $transformation = VehicleTransformation::where('id', request()->transformation_id)->first();
            $vehicle = $transformation->vehicle_id;
        }

        $tintedId = VehicleCharacteristic::where('value', GlassTypeEnum::tinted->value)->pluck('id')->first();
        $tintedAuthorization = TintedWindowAuthorization::where('vehicle_id', $vehicle)->first();
        if (in_array($tintedId, $characteristics) && (! $tintedAuthorization || ! in_array($tintedAuthorization->status, [Status::validated->value, Status::closed->value]))) {
            $fail("Désolé, vous devez disposer d'une autorisation de vitre teinté");
        }
    }
}
