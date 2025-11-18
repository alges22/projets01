<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Config\AlertTypeSeeder;
use Database\Seeders\Config\BorderSeeder;
use Database\Seeders\Config\CountriesSeeder;
use Database\Seeders\Config\ImmatriculationFormatSeeder;
use Database\Seeders\Config\ImmatriculationTypeSeeder;
use Database\Seeders\Config\InstitutionSeeder;
use Database\Seeders\Config\InstitutionTypeSeeder;
use Database\Seeders\Config\LegalStatusSeeder;
use Database\Seeders\Config\ManagementCenterSeeder;
use Database\Seeders\Config\ManagementCenterTypeSeeder;
use Database\Seeders\Config\NotificationConfigSeeder;
use Database\Seeders\Config\OrganizationSeeder;
use Database\Seeders\Config\OwnerTypeSeeder;
use Database\Seeders\Config\ParkSeeder;
use Database\Seeders\Config\PaymentProviderSeeder;
use Database\Seeders\Config\PlateColorSeeder;
use Database\Seeders\Config\PlateShapeSeeder;
use Database\Seeders\Config\ServiceSeeder;
use Database\Seeders\Config\ServiceTypeSeeder;
use Database\Seeders\Config\StepSeeder;
use Database\Seeders\Config\TitleReasonSeeder;
use Database\Seeders\Config\TitleReasonTypeSeeder;
use Database\Seeders\Config\TransformationTypeSeeder;
use Database\Seeders\Config\VehicleBrandSeeder;
use Database\Seeders\Config\VehicleCategorySeeder;
use Database\Seeders\Config\VehicleCharacteristicCategorySeeder;
use Database\Seeders\Config\VehicleEnergySourceSeeder;
use Database\Seeders\Config\VehicleTypeSeeder;
use Database\Seeders\Config\ZoneSeeder;
use Illuminate\Database\Seeder;
use Ntech\RequiredDocumentPackage\Database\Seeders\DocumentTypeSeeder;
use Ntech\UserPackage\Database\Seeders\AppModulesSeeder;
use Ntech\UserPackage\Database\Seeders\AuthConfigSeeder;
use Ntech\UserPackage\Database\Seeders\PositionSeeder;
use Ntech\UserPackage\Database\Seeders\RolesSeeder;
use Ntech\UserPackage\Database\Seeders\UsersSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AppModulesSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(ProfileTypeSeeder::class);
        $this->call(SpaceSeeder::class);
        $this->call(InstitutionTypeSeeder::class);
        $this->call(InstitutionSeeder::class);
        $this->call(PositionSeeder::class);
        $this->call(PlateColorSeeder::class);
        $this->call(BorderSeeder::class);
        $this->call(OrganizationSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(AuthConfigSeeder::class);
        $this->call(VehicleCharacteristicCategorySeeder::class);
        $this->call(DocumentTypeSeeder::class);
        $this->call(VehicleTypeSeeder::class);
        $this->call(FormatComponentSeeder::class);
        $this->call(NotificationConfigSeeder::class);
        $this->call(MetaDataSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(OwnerTypeSeeder::class);
        $this->call(LegalStatusSeeder::class);
        $this->call(VehicleBrandSeeder::class);
        $this->call(VehicleEnergySourceSeeder::class);
        $this->call(PlateShapeSeeder::class);
        $this->call(VehicleCategorySeeder::class);
        $this->call(StepSeeder::class);
        $this->call(ServiceTypeSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(ParkSeeder::class);
        $this->call(ManagementCenterTypeSeeder::class);
        $this->call(DeclarantSeeder::class);
        $this->call(VehicleCharacteristicSeeder::class);
        //$this->call(VehicleOwnerSeeder::class);
        //$this->call(VehicleSeeder::class);
        //$this->call(SaleDeclarationSeeder::class);
        //$this->call(PrestigeLabelImmatriculationSeeder::class);
        $this->call(NumberTemplateSeeder::class);
        $this->call(AlertTypeSeeder::class);
        $this->call(ZoneSeeder::class);
        $this->call(ManagementCenterSeeder::class);
        $this->call(ImmatriculationFormatSeeder::class);
        //$this->call(ImmatriculationSeeder::class);
        $this->call(ReimmatriculationReasonSeeder::class);
        $this->call(TitleReasonTypeSeeder::class);
        $this->call(TitleReasonSeeder::class);
        // $this->call(PlateSeeder::class);
        $this->call(ImmatriculationTypeSeeder::class);
        $this->call(TransformationTypeSeeder::class);
        $this->call(TransformationTypeVehicleCharacteristicSeeder::class);
        $this->call(PaymentProviderSeeder::class);
    }
}
