<?php

namespace Database\Seeders\Staff;

use App\Consts\Roles;
use App\Models\Account\User;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use App\Models\Config\Village;
use App\Models\Institution\Institution;
use App\Models\Space\Space;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\ProfileTypesEnum;
use Ntech\UserPackage\Models\Identity;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffs = [
            [
                'email' => 'bankeradmin@simveb.bj',
                'telephone' => '98894816',
                'firstname' => 'Peter',
                'lastname' => 'JOHNSON',
                'npi' => '8823456789',
            ],
        ];

        $village = Village::query()->first();
        $institution = Institution::query()->with('town')->first();
        $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
        $profileTypeBank = ProfileType::where('code', ProfileTypesEnum::bank->name)->first();

        foreach ($staffs as $staff) {
            $identityBanker = Identity::updateOrCreate([
                'email' => $staff['email'],
                'npi' => $staff['npi'],
            ], [
                'telephone' => '+229' . $staff['telephone'],
                'firstname' => $staff['firstname'],
                'lastname' => $staff['lastname'],
                'country_id' => 24,
                'state_id' => $village->district->town->state_id,
                'town_id' => $village->district->town_id,
                'district_id' => $village->district_id,
                'village_id' => $village->id,
            ]);

            $user = User::updateOrCreate([
                'username' => $staff['npi'],
                'email' => $staff['email'],
            ], [
                'email_verified_at' => now(),
                'identity_id' => $identityBanker->id,
                'password' => Hash::make('here is the pass'),
            ]);

            if (!$userProfile = $user->profiles()->where('type_id', $profileTypeUser->id)->first()) {
                Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeUser->id,
                    'identity_id' => $identityBanker->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeBank->id)->first()->id,
                ]);
            } else {
                $userProfile->update([
                    'identity_id' => $identityBanker->id,
                ]);
            }

            if (!$bankProfile = $user->profiles()->where('type_id', $profileTypeBank->id)->first()) {
                $bankProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeBank->id,
                    'identity_id' => $identityBanker->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeBank->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeBank->id)->first()->institution_id,
                ]);
            } else {
                $bankProfile->update([
                    'identity_id' => $identityBanker->id,
                ]);
            }
            $bankProfile->syncRoles(Roles::BANK);
        }
    }
}
