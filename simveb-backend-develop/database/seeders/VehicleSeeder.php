<?php

namespace Database\Seeders;

use App\Models\Config\OwnerType;
use App\Models\Config\Park;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleBrand;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleOwner;
use App\Models\Vehicle\VehicleType;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function getData()
    {
        $idBrand = VehicleBrand::pluck('id')->first();
        $idType = VehicleType::pluck('id')->first();
        $idCategory = VehicleCategory::pluck('id')->first();
        $idPark = Park::pluck('id')->first();
        $idOwnerType = OwnerType::pluck('id')->first();
        $idOwner = VehicleOwner::pluck('id')->first();
        //$characteristic = VehicleCharacteristic::pluck('id')->first();
        return [
            [
                "origin_country_id" => 24,
                "vin" => "544003410009",
                "vehicle_brand_id" => $idBrand,
                "vehicle_model" => "Toyota Prado",
                "number_of_seats" => 5,
                "vehicle_type_id" => $idType,
                "vehicle_category_id" => $idCategory,
                "engin_number" => "8565500035454",
                "charged_weight" => "800",
                "empty_weight" => "500",
                "first_circulation_year" => 2015,
                "park_id" => $idPark,
                "owner_type_id" => $idOwnerType,
                "owner_id" => $idOwner,
                /* "vehicle_characteristics" => [$characteristic] */
            ]
        ];
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $vehicle) {
            Vehicle::updateOrCreate($vehicle);
        }
    }
}
