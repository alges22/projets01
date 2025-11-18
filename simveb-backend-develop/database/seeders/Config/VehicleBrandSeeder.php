<?php

namespace Database\Seeders\Config;

use App\Models\Vehicle\VehicleBrand;
use Illuminate\Database\Seeder;

class VehicleBrandSeeder extends Seeder
{
    public function getData()
    {
        return [
            [
                'name' => 'Alfa Roméo',
                'description' => '',
                'native_country' => 'Italie',
            ],
            [
                'name' => 'Aston Martin',
                'description' => '',
                'native_country' => 'Angleterre',
            ],
            [
                'name' => 'Audi',
                'description' => '',
                'native_country' => 'Allemagne',
            ],
            [
                'name' => 'Bentley',
                'description' => '',
                'native_country' => 'Allemagne',
            ],
            [
                'name' => 'BMW',
                'description' => '',
                'native_country' => 'Allemagne',
            ],
            [
                'name' => 'Chevrolet',
                'description' => '',
                'native_country' => 'Etats-Unis',
            ],
            [
                'name' => 'Ferrari',
                'description' => '',
                'native_country' => 'Italie',
            ],
            [
                'name' => 'Ford',
                'description' => '',
                'native_country' => 'Etats-Unis',
            ],
            [
                'name' => 'Honda',
                'description' => '',
                'native_country' => 'Japon',
            ],
            [
                'name' => 'Hyundai',
                'description' => '',
                'native_country' => 'Corée du sud',
            ],
            [
                'name' => 'Jeep',
                'description' => '',
                'native_country' => 'Etats-Unis',
            ],
            [
                'name' => 'Kia',
                'description' => '',
                'native_country' => 'Corée du sud',
            ],
            [
                'name' => 'Lamborghini',
                'description' => '',
                'native_country' => 'Italie',
            ],
            [
                'name' => 'Maserati',
                'description' => '',
                'native_country' => 'Italie',
            ],
            [
                'name' => 'Toyota',
                'description' => '',
                'native_country' => 'Japon',
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            VehicleBrand::updateOrCreate([
                'name' => $data['name'],
            ], [
                'description' => $data['description'],
                'native_country' => $data['native_country'],
            ]);
        }
    }
}
