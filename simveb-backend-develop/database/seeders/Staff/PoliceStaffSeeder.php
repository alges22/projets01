<?php

namespace Database\Seeders\Staff;

use App\Enums\ProfileTypesEnum;
use App\Models\Account\User;
use App\Models\Space\Space;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use App\Models\Config\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ntech\UserPackage\Models\Identity;

class PoliceStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            [
                "npi" => 9876543210,
                "email" => "isabella@example.com",
                "telephone" => "61849888",
                "firstname" => "Isabella",
                "lastname" => "ANDERSON",
            ],
        ];
        $village = Village::query()->where('name', 'AGONTINKON')->first();

        $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
        $profileTypePolice = ProfileType::where('code', ProfileTypesEnum::police->name)->first();

        foreach ($members as $member) {
            $identity = Identity::updateOrCreate([
                'email' => $member['email'],
                'npi' => $member['npi'],
            ], [
                'telephone' => '+229' . $member['telephone'],
                'firstname' => $member['firstname'],
                'lastname' => $member['lastname'],
                'country_id' => 24,
                'state_id' => $village->district->town->state_id,
                'town_id' => $village->district->town_id,
                'district_id' => $village->district_id,
                'village_id' => $village->id,
            ]);

            $user = User::updateOrCreate([
                'email' => $member['email'],
                'username' => $member['npi'],
            ], [
                'email_verified_at' => now(),
                'identity_id' => $identity->id,
                'password' => Hash::make('here is the pass'),
            ]);

            if (!$userProfile = $user->profiles()->where('type_id', $profileTypeUser->id)->first()) {
                $userProfile = Profile::create([
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

            if (!$policeProfile = $user->profiles()->where('type_id', $profileTypePolice->id)->first()) {
                $policeProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypePolice->id,
                    'space_id' => Space::where('profile_type_id', $profileTypePolice->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypePolice->id)->first()->institution_id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $policeProfile->update([
                    'identity_id' => $identity->id,
                ]);
            }
            $policeProfile->syncRoles($profileTypePolice->roles()->pluck('name')->toArray());
        }
    }
}
