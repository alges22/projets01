<?php

namespace Database\Seeders\Config;

use App\Enums\InstitutionTypesEnum;
use App\Models\Institution\InstitutionType;
use Illuminate\Database\Seeder;

class InstitutionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (InstitutionTypesEnum::toNameValue() as $name => $value) {
            InstitutionType::updateOrCreate(
                ['name' => $name],
                ['description' => $value]
            );
        }
    }
}
