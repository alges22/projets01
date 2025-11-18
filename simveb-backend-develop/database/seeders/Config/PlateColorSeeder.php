<?php

namespace Database\Seeders\Config;

use App\Models\Plate\PlateColor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlateColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colorsData = [
            'Crème' => "#F5F5DC",
            'Bleue' => "#0000FF",
            'Rouge' => "#FF0000",
            'Verte' => "#008000",
            'Noire' => "#000000",
            'Orange' => "#FF7F00",
        ];

        foreach($colorsData as $label => $code) {
            PlateColor::updateOrCreate([
                'name' => Str::slug($label, '_'),
            ], [
                'label' => $label,
                'color_code' => $code,
                'text_color' => '#000',
            ]);
        }
    }
}
