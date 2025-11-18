<?php

namespace Database\Seeders\Config;

use App\Models\Config\TransformationType;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use Illuminate\Database\Seeder;
use function Ntech\ActivityLogPackage\Services\get;

class TransformationTypeSeeder extends Seeder
{

    public function getData()
    {
        return [
            [
                'label' => 'Esthétique',
                'description' => 'peinture, carrosserie, nombre de place',
            ],
            [
                'label' => 'Performance',
                'description' => 'énergie, cylindre, puissance',
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            TransformationType::updateOrCreate(
                [
                    'label' => $data['label'],
                ],
                [
                    'description' => $data['description'],
                ]
            );
        }
    }
}
