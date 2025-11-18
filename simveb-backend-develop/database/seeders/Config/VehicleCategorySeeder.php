<?php

namespace Database\Seeders\Config;

use App\Models\Vehicle\VehicleCategory;
use Illuminate\Database\Seeder;

class VehicleCategorySeeder extends Seeder
{
    public function getData()
    {
        return [
            [
                'name' =>  '2 ou 3 roues',
                'nb_plate' => '1',
            ],
            [
                'name' =>  '4 roues et plus',
                'nb_plate' => '2',
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            VehicleCategory::updateOrCreate([
                'name' => $data['name'],
            ], [
                'label' => $data['name'],
                'nb_plate' => $data['nb_plate'],
            ]);
        }
    }
}
