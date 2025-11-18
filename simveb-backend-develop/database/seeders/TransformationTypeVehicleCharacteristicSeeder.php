<?php

namespace Database\Seeders;

use App\Enums\CharacteristicCategoriesEnum;
use App\Enums\TransformationTypesEnum;
use App\Models\Config\TransformationType;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransformationTypeVehicleCharacteristicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transformationEsthetic = TransformationType::where('label', TransformationTypesEnum::aesthetics->value)->first();
        $transformationPerformance = TransformationType::where('label', TransformationTypesEnum::performance->value)->first();

        $categorieEsthetic = VehicleCharacteristicCategory::whereIn('code', [
            CharacteristicCategoriesEnum::paint->name,
            CharacteristicCategoriesEnum::color_1->name,
            CharacteristicCategoriesEnum::color_2->name,
            CharacteristicCategoriesEnum::color_3->name,
            CharacteristicCategoriesEnum::color_4->name,
            CharacteristicCategoriesEnum::bodyshop->name,
            CharacteristicCategoriesEnum::rim->name,
            CharacteristicCategoriesEnum::number_of_seats->name,
            CharacteristicCategoriesEnum::glass_type->name,
        ])->pluck('id')->toArray();

        $categoriePerformance = VehicleCharacteristicCategory::whereIn('code', [
            CharacteristicCategoriesEnum::vehicle_energy->name,
            CharacteristicCategoriesEnum::horsepower->name,
            CharacteristicCategoriesEnum::engine_power->name,
        ])->pluck('id')->toArray();


        $transformationEsthetic->categoryCharacteristics()->sync($categorieEsthetic);
        $transformationPerformance->categoryCharacteristics()->sync($categoriePerformance);

    }
}
