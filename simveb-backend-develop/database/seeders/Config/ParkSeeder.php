<?php

namespace Database\Seeders\Config;

use App\Models\Config\Park;
use Illuminate\Database\Seeder;

class ParkSeeder extends Seeder
{
    public function getData()
    {
        return [
            [
                "name" => "GLOSS DRIVE",
                "description" => "Your journey in luxury starts here",
                "address" => "Seme Kpodji",
                "longitude" => 228.0987,
                "latitude" => 304.0123,

            ],
            [
                "name" => "SWIFT CAR PARK",
                "description" => "Where speed meets elegance",
                "address" => "Seme Kpodji",
                "longitude" => 28.0987,
                "latitude" => 04.0123,

            ]
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data)
        {
            Park::updateOrCreate([
                "name" => $data['name']
            ],[
                "description" => $data['description'],
                "address" => $data['address'],
                "longitude" => $data['longitude'],
                "latitude" => $data['latitude'],
        
            ]);
        }
    }
}
