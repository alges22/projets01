<?php

namespace Database\Seeders;

use App\Models\Config\Service;
use App\Models\Immatriculation\Immatriculation;
use App\Repositories\PrestigeLabelImmatriculationRepository;
use Illuminate\Database\Seeder;

class PrestigeLabelImmatriculationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $repository = new PrestigeLabelImmatriculationRepository();
        $immatriculation = Immatriculation::query()->latest()->first();

        $demand = $repository->store([
            'desired_label' => "simveb",
            'immatriculation_id' => $immatriculation->id,
            'service_id' => Service::query()->first()->id,
            'payment_status' => 'approved',
            'reference' => 'SEED-STATEMENT-'.time()
        ]);

        $this->command->info("prestige label immatriculation created : $demand->id");
    }
}
