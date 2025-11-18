<?php

namespace Database\Seeders\Config;

use App\Models\Config\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{



    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations =  [
            [
                'name' => 'Immatriculation 4 roues ou plus',
                'description' => '',
            ],
            [
                'name' => 'Immatriculation 3 roues',
                'description' => '',
            ],
            [
                'name' => 'Immatriculation 2 roues',
                'description' => '',
            ],
            [
                'name' => 'Interpol',
                'description' => '',
                'is_interpol' => true
            ]
        ];

        foreach ($organizations as $organization)
        {
            Organization::query()->updateOrCreate($organization);
        }

    }
}
