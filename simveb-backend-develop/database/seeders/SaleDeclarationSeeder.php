<?php

namespace Database\Seeders;

use App\Models\Config\Service;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Repositories\SaleDeclarationRepository;
use Illuminate\Database\Seeder;

class SaleDeclarationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $repository = new SaleDeclarationRepository();
        $vehicle = Vehicle::query()->latest()->first();
        $vehicleOwner = VehicleOwner::query()->latest()->first();

        $demand = $repository->store([
            'vehicle_id' => $vehicle->id,
            'vehicle_owner_id' => $vehicle->owner_id,
            'new_vehicle_owner_id' => $vehicleOwner->id,
            'service_id' => Service::query()->first()->id,
            'payment_status' => 'approved',
            'reference' => 'SEED-STATEMENT-'.time()
        ]);

        $this->command->info("cession statement created : $demand->id");
    }
}
