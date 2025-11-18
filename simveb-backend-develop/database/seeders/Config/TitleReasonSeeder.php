<?php

namespace Database\Seeders\Config;

use App\Models\Config\TitleReason;
use App\Models\Config\TitleReasonType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TitleReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasonTypes = TitleReasonType::query()->pluck('id')->toArray();
        $reasons = [
            [
                "label" => "Changement de propriétaire",
                "description" => "Changement de propriétaire",
                "reason_type" => $reasonTypes[0],
            ],
            [
                "label" => "Changement d'adresse",
                "description" => "Changement d'adresse",
                "reason_type" => $reasonTypes[1],
            ],
            [
                "label" => "Fin de location/Leasing",
                "description" => "Fin de location/Leasing",
                "reason_type" => $reasonTypes[0],
            ],
            [
                "label" => "Conversion de titres",
                "description" => "Conversion de titres",
                "reason_type" => $reasonTypes[1],
            ],
            [
                "label" => "Transfert intra-familial",
                "description" => "Transfert intra-familial",
                "reason_type" => $reasonTypes[0],
            ],
        ];

        foreach ($reasons as $reason) {
            TitleReason::updateOrCreate(
                [
                    "label" => $reason["label"],
                ], [
                    "description" => $reason['description'],
                    "reason_type" => $reason['reason_type'],
                ]
            );
        }
    }
}
