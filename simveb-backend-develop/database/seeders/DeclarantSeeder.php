<?php

namespace Database\Seeders;

use App\Models\Account\Declarant;
use App\Models\Institution\Institution;
use Illuminate\Database\Seeder;
use Ntech\UserPackage\Models\Identity;

class DeclarantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutionId = Institution::pluck('id')->first();
        $identity = Identity::updateOrCreate(
            [
                "firstname" => "Olivier",
                "lastname" => "LAGARDE",
                "telephone" => "90000005",
                "email" => "olivier@gmail.com",
            ]);
        Declarant::updateOrCreate(
            [
                "identity_id" => $identity->id,
                "institution_id" => $institutionId
            ]);
    }
}
