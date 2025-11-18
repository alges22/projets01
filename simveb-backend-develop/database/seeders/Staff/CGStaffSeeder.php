<?php

namespace Database\Seeders\Staff;

use App\Consts\Roles;
use App\Models\Space\Space;
use App\Models\Account\User;
use App\Models\Auth\Profile;
use App\Models\Config\Village;
use App\Enums\ProfileTypesEnum;
use Illuminate\Database\Seeder;
use App\Models\Auth\ProfileType;
use Illuminate\Support\Facades\Hash;
use Ntech\UserPackage\Models\Identity;

class CGStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffs = [
            [
                "npi" => 3610987650,
                "email" => "mia@example.com",
                "telephone" => "12389012",
                "firstname" => "Mia",
                "lastname" => "THOMPSON",
            ],
        ];
        $village = Village::query()->where('name', 'AGONTINKON')->first();
        foreach ($staffs as $staff){
            $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
            $profileTypeCG = ProfileType::where('code', ProfileTypesEnum::central_garage->name)->first();
            $identity = Identity::updateOrCreate([
                'email' => $staff['email'],
                'npi' => $staff['npi'],
            ],
                [
                    'telephone' => '+229' . $staff['telephone'],
                    'firstname' => $staff['firstname'],
                    'lastname' => $staff['lastname'],
                    'country_id' => 24,
                    'state_id' => $village->district->town->state_id,
                    'town_id' => $village->district->town_id,
                    'district_id' => $village->district_id,
                    'village_id' => $village->id,
                ]);

            $user = User::query()->updateOrCreate([
                'username' => $staff['npi'],
                'email' => $staff['email'],
            ], [
                'email_verified_at' => now(),
                'identity_id' => $identity->id,
                'password' => Hash::make('here is the pass')
            ]);

            if (!$userProfile = $user->profiles()->where('type_id', $profileTypeUser->id)->first()) {
               Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeUser->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeUser->id)->first()->id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $userProfile->update([
                    'identity_id' => $identity->id,
                ]);
            }

            if (!$cgProfile = $user->profiles()->where('type_id', $profileTypeCG->id)->first()) {
                $cgProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeCG->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeCG->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeCG->id)->first()->institution_id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $cgProfile->update([
                    'identity_id' => $identity->id,
                ]);
            }

            $cgProfile->syncRoles([Roles::CG_ADMIN]);
        }


    }
}
