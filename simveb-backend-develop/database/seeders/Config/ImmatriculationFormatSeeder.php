<?php

namespace Database\Seeders\Config;

use App\Enums\ProfileTypesEnum;
use App\Models\Auth\ProfileType;
use App\Models\Immatriculation\FormatComponent;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Models\Vehicle\VehicleCategory;
use App\Services\Immatriculation\ImmatriculationFormatService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImmatriculationFormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $components = [];
        $motorcycleComponents = [];
        foreach (FormatComponent::all() as $component) {
            $components[] = [
                'id' => $component->id,
                'value' => '',
                'position' => count($components) + 1,
            ];
        }

        foreach (FormatComponent::all() as $component) {
            if (!($component->code == 'prefix')) {
                $motorcycleComponents[] = [
                    'id' => $component->id,
                    'value' => '',
                    'position' => count($motorcycleComponents) + 1,
                ];
            } else {
                $motorcycleComponents[] = [
                    'id' => $component->id,
                    'value' => '2',
                    'position' => count($motorcycleComponents) + 1,
                ];
            }
        }

        $immatriculationFormatsData = [
            [
                'vehicle_category_id' => VehicleCategory::where('nb_plate', 2)->first()->id,
                'profile_type_id' => ProfileType::where('code', ProfileTypesEnum::user->name)->first()->id,
                'components' => $components,
            ],
            [
                'vehicle_category_id' => VehicleCategory::where('nb_plate', 1)->first()->id,
                'profile_type_id' => ProfileType::where('code', ProfileTypesEnum::user->name)->first()->id,
                'components' => $motorcycleComponents,
            ]
        ];

        foreach ($immatriculationFormatsData as $data) {

            if (!$immatriculationFormat = ImmatriculationFormat::where('vehicle_category_id', $data['vehicle_category_id'])->where('profile_type_id', $data['profile_type_id'])->first()) {
                $immatriculationFormat = ImmatriculationFormat::create([
                    'vehicle_category_id' => $data['vehicle_category_id'],
                    'profile_type_id' => $data['profile_type_id'],
                    'format' => (new ImmatriculationFormatService)->generateFormatArray($data['components']),
                ]);

                foreach ($data['components'] as $component) {
                    $immatriculationFormat->components()->attach([$component['id'] =>
                    [
                        'value' => $component['value'] ?? null,
                        'position' => $component['position']
                    ]]);
                }
            }
        }
    }
}
