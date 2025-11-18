<?php

namespace Database\Seeders\Config;

use App\Models\Config\Town;
use App\Models\Config\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zoneData = [
            [
                'code' => '01',
                'name' => 'Littoral',
                'description' => null,
                'towns' => Town::whereRaw('LOWER(name) LIKE ?', ["%cotonou%"])->pluck('id')->toArray(),
            ],
            [
                'code' => '02',
                'name' => 'Calavi',
                'description' => null,
                'towns' => Town::whereRaw('LOWER(name) LIKE ?', ["%calavi%"])->pluck('id')->toArray(),
            ]
        ];

        foreach ($zoneData as $data) {
            $zone = Zone::updateOrCreate([
                'code' => $data['code'],
                'name' => $data['name'],
            ], [
                'description' => $data['description'] ?? null,
            ]);

            $zone->towns()->sync($data['towns'] ?? []);
        }
    }
}
