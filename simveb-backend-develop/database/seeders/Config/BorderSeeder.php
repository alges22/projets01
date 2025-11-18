<?php

namespace Database\Seeders\Config;

use App\Models\Config\Border;
use Illuminate\Database\Seeder;

class BorderSeeder extends Seeder
{
    public function getData()
    {
        return [
            [
                'name' => 'Frontière bénino-togolaise',
                'longitude' => 12.3456,
                'latitude' => 78.9101,
                'border_country_id' => 24,
                'town_id' => null,
            ],
            [
                'name' => 'Frontière bénino-nigériane',
                'longitude' => 12.3456,
                'latitude' => 78.9101,
                'border_country_id' => 161,
                'town_id' => null,
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            Border::updateOrCreate(
                [
                    'name' => $data['name'],
                    'town_id' => $data['town_id'],
                ], [
                    'longitude' => $data['longitude'],
                    'latitude' => $data['latitude'],
                    'border_country_id' => $data['border_country_id'],
            ]);
        }
    }
}
