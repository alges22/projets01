<?php

namespace Database\Seeders;

use App\Models\Config\Zone;
use App\Models\Immatriculation\FormatComponent;
use Illuminate\Database\Seeder;

class FormatComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding format components...');

        $components = [
            [
                'code' => FormatComponent::PREFIX,
                'description' => "Le préfixe",
                'default_length' => 2,
            ],
            [
                'code' => FormatComponent::ALPHABETIC_LABEL,
                'description' => "Les caractère alphabétiques",
                'default_length' => 2
            ],
            [
                'code' => FormatComponent::ZONE,
                'description' => "Code de la zone",
                'default_length' => 2,
                'possible_values' => Zone::query()->select('code')
                    ->pluck('code')
                    ->toArray()
            ],
            [
                'code' => FormatComponent::NUMERIC_LABEL,
                'description' => "Les caractère numérique",
                'default_length' => 4,
                'is_auto' => true,
            ],
            [
                'code' => FormatComponent::COUNTRY_CODE,
                'description' => "Le code pays",
                'default_length' => 2,
                'possible_values' => ['RB','BJ']
            ],
        ];

        foreach ($components as $component)
        {
            FormatComponent::query()->updateOrCreate(['code' => $component['code']], $component);
        }

        $this->command->info('Format components seeding done');

    }
}
