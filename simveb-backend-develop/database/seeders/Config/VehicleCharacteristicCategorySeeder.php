<?php

namespace Database\Seeders\Config;

use App\Enums\CharacteristicCategoriesEnum;
use App\Enums\VehicleCharacteristicCategoryType;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleCharacteristicCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesData = [
            [
                'label' => 'Energie',
                'code' => CharacteristicCategoriesEnum::vehicle_energy->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::vehicle_energy->name,
            ],
            [
                'label' => 'Puissance du moteur',
                'code' => CharacteristicCategoriesEnum::engine_power->name,
                'type' => VehicleCharacteristicCategoryType::interval->name,
                'field_name' => CharacteristicCategoriesEnum::engine_power->name,
            ],
            [
                'label' => 'Type de vitre',
                'code' => CharacteristicCategoriesEnum::glass_type->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::glass_type->name,
            ],
            [
                'label' => 'Jante',
                'code' => CharacteristicCategoriesEnum::rim->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::rim->name,
            ],
            [
                'label' => 'Modèle',
                'code' => CharacteristicCategoriesEnum::vehicle_model->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::vehicle_model->name,
            ],
            [
                'label' => 'Nombre de place assise',
                'code' => CharacteristicCategoriesEnum::number_of_seats->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::number_of_seats->name,
            ],
            [
                'label' => 'Poids à vide',
                'code' => CharacteristicCategoriesEnum::empty_weight->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::empty_weight->name,
            ],
            [
                'label' => 'Poids à charge',
                'code' => CharacteristicCategoriesEnum::charged_weight->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::charged_weight->name,
            ],
            [
                'label' => 'Nombre de chevaux',
                'code' => CharacteristicCategoriesEnum::horsepower->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::horsepower->name,
            ],
            [
                'label' => 'Peinture',
                'code' => CharacteristicCategoriesEnum::paint->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::paint->name,
            ],
            [
                'label' => 'Carrosserie',
                'code' => CharacteristicCategoriesEnum::bodyshop->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::bodyshop->name,
            ],
            [
                'label' => 'Couleur 1',
                'code' => CharacteristicCategoriesEnum::color_1->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::color_1->name,
            ],
            [
                'label' => 'Couleur 2',
                'code' => CharacteristicCategoriesEnum::color_2->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::color_2->name,
            ],
            [
                'label' => 'Couleur 3',
                'code' => CharacteristicCategoriesEnum::color_3->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::color_3->name,
            ],
            [
                'label' => 'Couleur 4',
                'code' => CharacteristicCategoriesEnum::color_4->name,
                'type' => VehicleCharacteristicCategoryType::string->name,
                'field_name' => CharacteristicCategoriesEnum::color_4->name,
            ],
        ];

        foreach($categoriesData as $categoryData) {
            VehicleCharacteristicCategory::updateOrCreate([
                'name' => Str::slug($categoryData['label'], '_'),
            ], [
                'label' => $categoryData['label'],
                'code' => $categoryData['code'],
                'type' => $categoryData['type'],
                'field_name' => $categoryData['field_name'] ?? null,
            ]);
        }
    }
}
