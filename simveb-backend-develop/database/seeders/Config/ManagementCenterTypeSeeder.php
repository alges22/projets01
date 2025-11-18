<?php

namespace Database\Seeders\Config;

use App\Models\Config\ManagementCenterType;
use Illuminate\Database\Seeder;

class ManagementCenterTypeSeeder extends Seeder
{

    public function getData()
    {
        return [
            [
                "label" => "Centre de gestion géographique",
                "description" => "Centre de gestion géographiques",
            ],
            [
                "label" => "Centre de gestion par nature",
                "description" => "Centre de gestion par nature",
            ]
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            ManagementCenterType::updateOrCreate(
                [
                    'label' => $data['label'],
                ],
                [
                    'description' => $data['description']
                ]
            );
        }
    }
}
