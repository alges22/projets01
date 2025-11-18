<?php

namespace App\Rules;

use App\Models\Vehicle\VehicleCharacteristic;
use App\Repositories\Vehicle\VehicleRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CompareCategoriesByCharacteristicsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $characteristics = request()->input('value_id', []);
        $vehicleRepository = new VehicleRepository;
        $vehicle = $vehicleRepository->findWhere(['vin' => request()->vin]);
        $vehicleCharacteristics = $vehicle->characteristics->pluck('id')->toArray();

        foreach ($characteristics as $characteristic) {
            if (in_array($characteristic, $vehicleCharacteristics)) {
                $fail("Au moins une caractéristique existe déjà.");
            }
        }

        $vehicleCategories = VehicleCharacteristic::with('category')
            ->whereIn('id', $characteristics)
            ->get();

        $categories = [];
        foreach ($vehicleCategories as $vehicleCategory) {
            $categoryId = $vehicleCategory->category->id;
            if (in_array($categoryId, $categories)) {
                $fail("Plusieurs caractéristiques appartiennent à la même catégorie.");
            }
            $categories[] = $categoryId;
        }
    }
}
