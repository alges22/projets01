<?php

namespace App\Rules;

use App\Enums\Status;
use App\Models\TransformationCharacteristic;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\VehicleTransformation;
use App\Repositories\Vehicle\VehicleRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckTransformationExistRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $characteristics = request()->input('value_id', []);
        $vehicleRepository = new VehicleRepository;
        $vehicle = $vehicleRepository->findWhere(['vin' => request()->vin]);

        if (!$vehicle) {
            $fail("Véhicule introuvable");
            return;
        }

        $vehicleId = $vehicle->id;

        $currentTransformations = VehicleTransformation::with('demand')
            ->whereHas('demand', function ($query) use ($vehicleId) {
                $query->where([
                    ['status', '!=', Status::closed->name],
                    ['vehicle_id', $vehicleId]
                ]);
            })->get();

        $transformationIds = $currentTransformations->pluck('id');
        $existingCharacteristics = TransformationCharacteristic::whereIn('transformation_id', $transformationIds)
            ->pluck('new_characteristic')
            ->toArray();

        $allCategories = VehicleCharacteristic::whereIn('id', array_merge($existingCharacteristics, $characteristics))
            ->pluck('category_id', 'id')
            ->toArray();

        $oldCategories = array_intersect_key($allCategories, array_flip($existingCharacteristics));
        $newCategories = array_intersect_key($allCategories, array_flip($characteristics));

        if (!empty(array_intersect($oldCategories, $newCategories))) {
            $fail("Impossible, une transformation de cette catégorie est déjà en cours.");
            return;
        }

        $vehicleCharacteristicExist = $vehicle->characteristics()->whereIn('vehicle_characteristic_id', $characteristics)->exists();

        if ($vehicleCharacteristicExist) {
            $fail("Impossible, ceci est une caractéristique actuelle de votre véhicule");
            return;
        }

        $categoryIds = array_unique(array_values($allCategories));

        if (count($categoryIds) !== count($allCategories)) {
            $fail("Plusieurs caractéristiques appartiennent à la même catégorie.");
        }
    }

    //}
}
