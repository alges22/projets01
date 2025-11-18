<?php


namespace Ntech\UserPackage\Database\Seeders;

use Database\Seeders\SpaceSeeder;
use Database\Seeders\ApprovedSeeder;
use Database\Seeders\InterpolMemberSeeder;
use Database\Seeders\Staff\ClerkSeeder;
use Database\Seeders\Staff\DistributorSeeder;
use Database\Seeders\Staff\AffiliateStaffSeeder;
use Database\Seeders\Staff\AnattAdminSeeder;
use Database\Seeders\Staff\AnattStaffSeeder;
use Database\Seeders\Staff\AuctioneerStaffSeeder;
use Database\Seeders\Staff\BankSeeder;
use Database\Seeders\Staff\CGStaffSeeder;
use Database\Seeders\Staff\GMAStaffSeeder;
use Database\Seeders\Staff\GMDStaffSeeder;
use Database\Seeders\Staff\InterpolStaffSeeder;
use Database\Seeders\Staff\InvestigatingJudgeSeeder;
use Database\Seeders\Staff\PoliceStaffSeeder;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AnattAdminSeeder::class);
        $this->call(PoliceStaffSeeder::class);
        $this->call(ApprovedSeeder::class); // agréé
        $this->call(InterpolStaffSeeder::class);
        $this->call(AnattStaffSeeder::class);
        $this->call(CGStaffSeeder::class);
        $this->call(AuctioneerStaffSeeder::class);
        $this->call(GMAStaffSeeder::class);
        $this->call(GMDStaffSeeder::class);
        $this->call(BankSeeder::class); // Banque
        //$this->call(ClerkSeeder::class); // Greffier
        //$this->call(InvestigatingJudgeSeeder::class); // Juge d'instruction
        $this->call(DistributorSeeder::class); // Concessionnaire
        $this->call(AffiliateStaffSeeder::class);
    }
}
