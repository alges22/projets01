<?php

namespace Database\Seeders\Config;

use App\Models\Config\OwnerType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OwnerTypeSeeder extends Seeder
{
    public function getData()
    {
        return [
            'Particulier',
            'ONG',
            'Maire',
            'Ambassadeur',
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $label) {
            OwnerType::updateOrCreate([
                'name' => Str::slug($label, '_'),
            ], [
                'label' => $label,
            ]);
        }
    }
}
