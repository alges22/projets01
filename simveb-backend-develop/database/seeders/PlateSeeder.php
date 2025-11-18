<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use  App\Models\Immatriculation\Immatriculation;
use Illuminate\Support\Str;
use App\Models\Plate\PlateShape;
use App\Models\Plate\PlateColor;


class PlateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $immatriculations = Immatriculation::all()->take(10);

        foreach ($immatriculations as $immatriculation) {
            $plateShape = PlateShape::first();
            $plateColor = PlateColor::first();

            $immatriculation->plates()->create([
                'serial_number' => Str::random(32),
                'plate_shape_id' => $plateShape->id,
                'plate_color_id' => $plateColor->id
            ]);

            $immatriculation->plates()->create([
                'serial_number' => Str::random(32),
                'plate_shape_id' => $plateShape->id,
                'plate_color_id' => $plateColor->id
            ]);
        }
    }
}
