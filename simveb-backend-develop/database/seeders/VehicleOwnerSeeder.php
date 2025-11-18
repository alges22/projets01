<?php

namespace Database\Seeders;

use App\Models\Config\LegalStatus;
use App\Models\Config\OwnerType;
use App\Models\Config\Town;
use App\Models\Vehicle\VehicleOwner;
use Illuminate\Database\Seeder;

class VehicleOwnerSeeder extends Seeder
{
    public function getData()
    {
        $idType  = OwnerType::pluck('id')->first();
        $idTown  = Town::pluck('id')->first();
        $idLegalStatus  = LegalStatus::pluck('id')->first();
        return [
            [
                "name" => "Jean KOKOU",
                "ifu" => "1456327005632",
                "npi" => "8500046551",
                "bfu" => "585008585",
                "state_id" => 3070,
                "telephone" => "+22996455088",
                "email" => "kokou@jean.com",
                "owner_type_id" => $idType,
                "town_id" => $idTown,
                "legal_status_id" => $idLegalStatus
            ]
        ];
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $owner) {
            VehicleOwner::updateOrCreate($owner);
        }
    }
}
