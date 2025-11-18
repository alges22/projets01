<?php

namespace Database\Seeders\Staff;

use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;
use App\Models\Account\User;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use App\Models\Config\Village;
use App\Models\Space\Space;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ntech\UserPackage\Models\Identity;
use Ntech\UserPackage\Models\Position;

class AffiliateStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffs = [
            [
                "npi" => 7109876540,
                "email" => "alexander@example.com",
                "telephone" => "65000001",
                "firstname" => "Alexander",
                "lastname" => "LEWIS",
            ],
        ];
        $village = Village::query()->where('name', 'AGONTINKON')->first();
        foreach ($staffs as $staff) {
            $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
            $profileTypeAffiliate = ProfileType::where('code', ProfileTypesEnum::affiliate->name)->first();
            $identity = Identity::updateOrCreate(
                [
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
                ]
            );

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
                    'institution_id' => Space::where('profile_type_id', $profileTypeUser->id)->first()->institution_id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $userProfile->update([
                    'identity_id' => $identity->id,
                ]);
            }

            if (!$affiliateProfile = $user->profiles()->where('type_id', $profileTypeAffiliate->id)->first()) {
                $affiliateProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeAffiliate->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeAffiliate->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeAffiliate->id)->first()->institution_id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $affiliateProfile->update([
                    'identity_id' => $identity->id,
                ]);
            }
            $affiliateProfile->syncRoles([Roles::AFFILIATE_MEMBER]);
        }
    }
}
