<?php

namespace App\Rules;

use App\Models\TintedWindowAuthorization;
use App\Models\TransformationCharacteristic;
use App\Models\Vehicle\VehicleCharacteristic;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Enums\Status;

class AddCharacteristicsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $characteristics = request()->input('value_id', []);
        $transformationCharacteristics = TransformationCharacteristic::where('transformation_id', request()->transformation_id)->get();
        if (!$transformationCharacteristics->isEmpty()) {
            $transformationCharacteristic = $transformationCharacteristics->first();
            $demand = $transformationCharacteristic->transformation->demand;

            if (in_array($demand->status,  [Status::validated->name, Status::print_order_emitted->name, Status::closed->name])) {
                $fail("Demande déjà validée ou en cours de traitement, vous ne pouvez pas mettre à jour");
            }

            $vehicle = $transformationCharacteristic->transformation->vehicle;

            $changeCharacteristicsExist = $transformationCharacteristics
                ->pluck('new_characteristic')
                ->toArray();

            $mergeCharacteristics = array_merge($characteristics, $changeCharacteristicsExist);
            $categoriesMap = VehicleCharacteristic::whereIn('id', $mergeCharacteristics)
                ->pluck('category_id', 'id')
                ->toArray();
            $categories = array_map(function ($characteristicId) use ($categoriesMap) {
                return $categoriesMap[$characteristicId] ?? null;
            }, $mergeCharacteristics);

            if (count($categories) !== count(array_unique($categories))) {
                $fail("Impossible, une transformation de cette catégorie est en cours. Mettez à jour plutôt !");
            }

            $vehicleCharacteristicExist = $vehicle->characteristics()->whereIn('vehicle_characteristic_id', $characteristics)->exists();

            if ($vehicleCharacteristicExist) {
                $fail("Impossible, ceci est une caractéristique actuelle de votre véhicule");
            }

            $categoryIds = VehicleCharacteristic::whereIn('id', $characteristics)
                ->with('category:id')
                ->get()
                ->pluck('category.id')
                ->toArray();

            if (count($categoryIds) !== count(array_unique($categoryIds))) {
                $fail("Plusieurs caractéristiques appartiennent à la même catégorie.");
            }
        }
    }
}
