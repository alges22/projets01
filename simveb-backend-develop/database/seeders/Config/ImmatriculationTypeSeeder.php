<?php

namespace Database\Seeders\Config;

use App\Enums\ImmatriculationTypeEnum;
use App\Models\Config\ImmatriculationType;
use App\Models\Plate\PlateColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImmatriculationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = PlateColor::select(['id', 'name', 'label'])->get();
        $data = [
            [
                'code' => ImmatriculationTypeEnum::gov->name,
                'plate_colors' => $colors->whereIn('name', ['creme', 'bleue', 'rouge'])->pluck('id')->toArray(),
            ],
            [
                'code' => ImmatriculationTypeEnum::diplomatic->name,
                'plate_colors' => $colors->where('name', 'creme')->pluck('id')->first(),
            ],
            [
                'code' => ImmatriculationTypeEnum::mai->name,
                'plate_colors' => $colors->whereIn('name', ['creme', 'verte', 'noire'])->pluck('id')->toArray(),

            ],
            [
                'code' => ImmatriculationTypeEnum::common->name,
                'plate_colors' => $colors->whereIn('name', ['creme', 'orange'])->pluck('id')->toArray(),

            ],
        ];

        foreach (ImmatriculationTypeEnum::toNameValue() as $code => $label) {
            $type = ImmatriculationType::updateOrCreate(
                ['label' => $label],
                ['code' => $code]
            );

            foreach ($data as $value) {
                if ($value['code'] === $code) {
                    $type->plateColors()->sync($value['plate_colors']);
                }
            }
        }
    }
}
