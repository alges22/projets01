<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ntech\MetadataPackage\Enums\MetaDataKeys;
use Ntech\MetadataPackage\Models\MetaData;

class MetaDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Seeding meta data");

        $durationsMetas = [
            ["key" => MetaDataKeys::space_token_duration->name, "value" => 24, "label" => "Durée de validité du token de lien d'enregistrement d'un compte affilié."],
        ];

        $metas = [
            MetaDataKeys::duration_params->name => ["data" => $durationsMetas, "label" => "Paramètre de durée de validité"],
        ];

        foreach ($metas as $key => $data)
        {
            MetaData::query()->updateOrCreate(['name' => $key],
            [
                "name" => $key,
                "label" => $data['label'],
                "data" => $data['data']
            ]);
        }

        $this->command->info("Meta data seeder completed");
    }
}
