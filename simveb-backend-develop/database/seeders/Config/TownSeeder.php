<?php

namespace Database\Seeders\Config;

use App\Models\Config\Town;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TownSeeder extends Seeder
{

    public function getData()
    {
        return [
            'Banikoara',
            'Gogounou',
            'Kandi',
            'Malanville',
            'Segbana',
            'Boukoumbé',
            'Cobly',
            'Kérou',
            'Kouandé',
            'Matéri',
            'Kérou',
            'Natitingou',
            'Péhounco',
            'Tanguiéta',
            'Toucountouna',
            'Cotonou',
            'Abomey-Calavi',
            'Porto-Novo',
        ];
    }


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $name) {
            Town::updateOrCreate([
                'code' => Str::slug($name, '_'),
            ], [
                'name' => $name,
            ]);
        }
    }
}
