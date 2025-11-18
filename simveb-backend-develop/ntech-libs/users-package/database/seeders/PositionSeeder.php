<?php

namespace Ntech\UserPackage\Database\Seeders;

use Illuminate\Database\Seeder;
use Ntech\UserPackage\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $positions = [
            "Administrateur système" => "Administrateur système",
            "Directeur général" => "Directeur général",
            "Chef service" => "Chef service",
        ];

        foreach ($positions as $name => $description) {
            Position::query()->updateOrCreate(
                ['name' => $name],
                [
                    'name' => $name,
                    'description' => $description,
                ]
            );
        }
    }
}
