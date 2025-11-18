<?php

namespace Database\Seeders\Config;

use App\Models\Config\LegalStatus;
use Illuminate\Database\Seeder;

class LegalStatusSeeder extends Seeder
{
    public function getData()
    {
        return [
            'Personne physique' => 'Personne physique',
            'Personne morale' => 'Personne morale',
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $name => $description) {
            LegalStatus::updateOrCreate([
                'name' => $name,
            ], [
                'description' => $description,
            ]);
        }
    }
}
