<?php

namespace Database\Seeders;

use App\Enums\GenderEnum;
use Illuminate\Support\Str;
use App\Models\Order\Demand;
use App\Models\Config\Country;
use App\Models\Config\Service;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Vehicle\VehicleOwner;
use Ntech\UserPackage\Models\Identity;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Immatriculation\ImmatriculationFormat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ImmatriculationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $vehicle = $this->getVehicleData();

        // Immatriculation::updateOrCreate([
        //     "vehicle_id" => $vehicle->id,
        //     "vehicle_owner_id" => $vehicle->owner_id
        // ], [
        //     "number" => 'AB1234',
        // ]);
    }

    /**
     *
     */
    public function getVehicleData()
    {
        $identity = Identity::updateOrCreate(
            [
                "email" => "fulbertlok@gmail.com",
                "telephone" => "+22997262524",
                "npi" => "4862579310",
            ],
            [
                "firstname" => "Fulbert",
                "lastname" => "LOKOSSOU",
                "birthdate" => date_create('1990-05-09'),
                "birth_place" => "Cotonou",
                "gender" => GenderEnum::M->name,
                "country_id" => DB::table('countries')->where('name', 'Benin')->pluck('id')->first(),
                "village_id" => DB::table('villages')->inRandomOrder()->pluck('id')->first(),
                "house" => "Carré. 1144"
            ]
        );

        $vehicleOwner = VehicleOwner::updateOrCreate([
            "bfu" => "191960000",
        ], [
            "legal_status" => DB::table('legal_statuses')->pluck('id')->first(),
            "identity_id" => $identity->id,
        ]);

        $vehicleData = Vehicle::updateOrCreate([
            "vin" => "012012012012",
            "vehicle_model" => "Série 1 M135i xDrive",
            "number_of_seats" => 5,
            "engin_number" => "0131313131313",
            "charged_weight" => "950",
            "empty_weight" => "500",
            "first_circulation_year" => 2015,
        ], [
            "origin_country_id" => DB::table('countries')->inRandomOrder()->pluck('id')->first(),
            "vehicle_brand_id" => DB::table('vehicle_brands')->inRandomOrder()->pluck('id')->first(),
            "vehicle_type_id" => DB::table('vehicle_types')->inRandomOrder()->pluck('id')->first(),
            "vehicle_category_id" => DB::table('vehicle_categories')->inRandomOrder()->pluck('id')->first(),
            "park_id" => DB::table('parks')->inRandomOrder()->pluck('id')->first(),
            "owner_type_id" => $vehicleOwner->owner_type_id,
            "owner_id" => $vehicleOwner->id,
        ]);

        $characteristics = VehicleCharacteristic::where('value', "essence")->orwhere('value', "1000")->orWhere('value', "Transparent")->pluck('id')->toArray();

        $vehicleData->characteristics()->attach($characteristics);

        return $vehicleData;
    }
}
