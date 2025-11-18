<?php

namespace Database\Seeders\Config;

use App\Models\Vehicle\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colorsData = [
            'Neuf',
            'Occasion',
        ];

        foreach($colorsData as $label) {
            VehicleType::updateOrCreate([
                'name' => Str::slug($label, '_'),
            ], [
                'label' => $label,
            ]);
        }
    }
}
