<?php

namespace Database\Seeders\Staff;

use App\Consts\Roles;
use App\Models\Account\User;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use App\Models\Config\Village;
use App\Models\Space\Space;
use App\Enums\ProfileTypesEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ntech\UserPackage\Models\Identity;

class InvestigatingJudgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffs = [
            [
                'email' => 'investigatingjudge@simveb.bj',
                'telephone' => '90006200',
                'firstname' => 'Mike',
                'lastname' => 'Dreffus',
                'npi' => '9018500210',
            ],
        ];

        $village = Village::query()->first();
        $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
        $profileTypeJudge = ProfileType::where('code', ProfileTypesEnum::court->name)->first();

        foreach ($staffs as $staff)
        {
            $judgeIdentity = Identity::updateOrCreate([
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
                'identity_id' => $judgeIdentity->id,
                'password' => Hash::make('here is the pass')
            ]);

            if (!$userProfile = $user->profiles()->where('type_id', $profileTypeUser->id)->first()) {
                Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeUser->id,
                    'identity_id' => $judgeIdentity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeJudge->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeJudge->id)->first()->institution_id,
                ]);
            } else {
                $userProfile->update([
                    'identity_id' => $judgeIdentity->id,
                ]);
            }

            if (!$judgeProfile = $user->profiles()->where('type_id', $profileTypeJudge->id)->first()) {
                $judgeProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeJudge->id,
                    'identity_id' => $judgeIdentity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeJudge->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeJudge->id)->first()->institution_id,
                ]);
            } else {
                $judgeProfile->update([
                    'identity_id' => $judgeIdentity->id,
                ]);
            }
            $judgeProfile->syncRoles(Roles::INVESTIGATING_JUDGE);
        }
    }
}
