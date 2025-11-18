<?php

namespace Database\Seeders;

use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Enums\GlassTypeEnum;
use App\Enums\CharacteristicCategoriesEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleCharacteristicSeeder extends Seeder
{

    public function generateBodyShop()
    {
        $startYear = 1995;
        $currentYear = date('Y');
        $bodieShop = [];
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $bodieShop[$year] = "Version $year";
        }
        return $bodieShop;
    }

    public function getData()
    {
        $colors = [
            "Pantone 426C" => "#1D1D1B",
            "Pantone 7540C" => "#2A2A29",
            "Pantone 421C" => "#D1D3D4",
            "Pantone 446C" => "#4B4F53",
            "Pantone 429C" => "#A7A8AA",
            "Pantone 430C" => "#7A7F80",
            "Pantone CoolGray9C" => "#6A6A6A",
            "Pantone 7621C" => "#D50032",
            "Pantone 485C" => "#D33E49",
            "Pantone 186C" => "#C8102E",
            "Pantone 7541C" => "#D1D8D9",
            "Pantone 11-0601TCX" => "#F1F1F1",
            "Pantone 295C" => "#003B5C",
            "Pantone 534C" => "#001F3D",
            "Pantone 653C" => "#1D3D5C",
            "Pantone 300C" => "#009CDE",
            "Pantone 7729C" => "#A7D08A",
            "Pantone 362C" => "#4C9B4A",
            "Pantone 7408C" => "#FF6F20",
            "Pantone 871C" => "#7A5C29"
        ];

        $data =  [
            [
                "category_id" => VehicleCharacteristicCategory::where('name','type_de_vitre')->first()->id,
                "value" => GlassTypeEnum::tinted->value,
                "min_value" => null,
                "max_value" => null,
                "code" => null,
            ],
            [
                "category_id" => VehicleCharacteristicCategory::where('name','type_de_vitre')->first()->id,
                "value" => GlassTypeEnum::transparent->value,
                "min_value" => null,
                "max_value" => null,
                "code" => null,
            ],
            [
                "category_id" => VehicleCharacteristicCategory::where('name','puissance_du_moteur')->first()->id,
                "value" => "1000",
                "min_value" => "200",
                "max_value" => "1000",
                "code" => null,
            ],
            [
                "category_id" => VehicleCharacteristicCategory::where('name','energie')->first()->id,
                "value" => "essence",
                "min_value" => null,
                "max_value" => null,
                "code" => null,
            ],
            [
                "category_id" => VehicleCharacteristicCategory::where('name','energie')->first()->id,
                "value" => "Hybride",
                "min_value" => null,
                "max_value" => null,
                "code" => null,
            ],
        ];
        foreach (['couleur_1', 'couleur_2', 'couleur_3', 'couleur_4'] as $colorCategory) {
            $categoryId = VehicleCharacteristicCategory::where('name', $colorCategory)->first()->id;

            foreach ($colors as $key => $code) {
                $data[] = [
                    "category_id" => $categoryId,
                    "value" => $key,
                    "code" => $code,
                    "min_value" => null,
                    "max_value" => null,
                ];
            }
        }

        foreach ($this->generateBodyShop() as $key => $value) {
            $categoryId = VehicleCharacteristicCategory::where('name', Str::slug(CharacteristicCategoriesEnum::bodyshop->value))->first()->id;
            $data[] = [
                "category_id" => $categoryId,
                "value" => $value,
                "code" => $key,
                "min_value" => null,
                "max_value" => null,
            ];
        }

        return $data;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            VehicleCharacteristic::updateOrCreate(
                [
                    'category_id' => $data['category_id'],
                    'value' => $data['value'],
                ],[
                    'min_value' => $data['min_value'],
                    'max_value' => $data['max_value'],
                    'code' => $data['code'],
                ]
            );
        }
    }
}
