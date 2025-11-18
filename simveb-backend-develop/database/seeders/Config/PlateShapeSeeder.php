<?php

namespace Database\Seeders\Config;

use App\Models\Plate\PlateShape;
use Illuminate\Database\Seeder;

class PlateShapeSeeder extends Seeder
{
    public function getData()
    {
        return [
            [
                'code' => 'rectangle',
                'name' => 'Rectangulaire',
                'description' => '',
                'cost' => 10000,
            ],
            [
                'code' => 'square',
                'name' => 'Carrée',
                'description' => '',
                'cost' => 10000,
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            PlateShape::updateOrCreate([
                'name' => $data['name'],
            ], [
                'description' => $data['description'],
                'cost' => $data['cost'],
                'code' => $data['code'],
            ]);
        }
    }
}
