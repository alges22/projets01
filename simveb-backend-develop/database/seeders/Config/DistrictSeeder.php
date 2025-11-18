<?php

namespace Database\Seeders\Config;

use App\Models\Config\District;
use App\Models\Config\Town;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function getData()
    {
        $idTown  = Town::pluck('id')->take(3)->toArray();
        return [
            [
                'code' => '1A',
                'name' => '1er Arrondissement',
                'town_id' => $idTown[0],
            ],
            [
                'code' => '2A',
                'name' => '2e Arrondissement',
                'town_id' => $idTown[1],
            ],
            [
                'code' => '3A',
                'name' => '3e Arrondissement',
                'town_id' => $idTown[2],
            ],
        ];
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            District::updateOrCreate(
                [
                    'code' => $data['code'],
                    'name' => $data['name'],
                ],[
                    'town_id' => $data['town_id'],
                ]
            );
        }
    }
}
