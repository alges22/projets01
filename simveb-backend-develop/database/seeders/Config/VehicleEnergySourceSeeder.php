<?php

namespace Database\Seeders\Config;

use App\Models\Vehicle\VehicleEnergySource;
use Illuminate\Database\Seeder;

class VehicleEnergySourceSeeder extends Seeder
{
    public function getData()
    {
        return [
            [
                'name' => 'Essence',
                'description' => 'Essence.',
            ],
            [
                'name' => 'Hybride',
                'description' => 'Essence électricité (hybride rechargeable), Essence-électricité (hybride non rechargeable), Gazole-électricité (hybride rechargeable), Gazole-électricité (hybride non rechargeable).',
            ],[
                'name' => 'Gazole',
                'description' => 'Gasoil ou Diesel.',
            ],[
                'name' => 'Électricité',
                'description' => 'Électricité.',
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            VehicleEnergySource::updateOrCreate([
                'name' => $data['name'],
            ], [
                'description' => $data['description'],
            ]);
        }
    }
}
