<?php

namespace Database\Seeders;

use App\Models\Config\Service;
use App\Models\Immatriculation\Immatriculation;
use App\Repositories\Duplicate\DuplicatePlateRepository;
use Illuminate\Database\Seeder;

class PlateDuplicateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $repository = new DuplicatePlateRepository();
        $immatriculation = Immatriculation::query()->first();

         $demand = $repository->store([
            'immatriculation_id' => $immatriculation->id,
            'vehicle_owner_id' => $immatriculation->vehicle->owner->id,
            'is_spoiled' => true,
            'service_id' => Service::query()->first()->id,
            'payment_status' => 'approved',
            'reference' => 'SEED-DUPLICATE-'.time()
        ]);

         $this->command->info("Plate duplicate created : $demand->id");

    }
}
