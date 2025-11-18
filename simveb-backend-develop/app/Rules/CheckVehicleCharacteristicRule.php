<?php

namespace App\Rules;

use App\Models\TransformationCharacteristic;
use App\Models\VehicleTransformation;
use App\Enums\Status;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use function Laravel\Pail\exists;
use function League\Uri\UriTemplate\first;
use function Ntech\ActivityLogPackage\Services\get;

class CheckVehicleCharacteristicRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $characteristicId = request()->transformation_characteristic_id;
        $transformationCharacteristic = TransformationCharacteristic::find($characteristicId);
        if ($transformationCharacteristic) {
            $vehicle = $transformationCharacteristic->transformation->vehicle;
            $demand = $transformationCharacteristic->transformation->demand;

            if (in_array($demand->status,  [Status::validated->name, Status::print_order_emitted->name, Status::closed->name])) {
                $fail("Impossible de mettre à jour, demande en cours de traitement ou déjà validée");
            }
            $transformationCharacteristicExist = $transformationCharacteristic->newCharacteristic()->where('id', $value)->exists();

            if($transformationCharacteristicExist) {
                $fail("Impossible, une demande de transformation de cette caractéristique est déjà en cours");
            }

            $vehicleCharacteristicExist = $vehicle->characteristics()->where('vehicle_characteristic_id', $value)->exists();

            if($vehicleCharacteristicExist) {
                $fail("Impossible, ceci est une caractéristique actuelle de votre véhicule");
            }
        }
    }
}
