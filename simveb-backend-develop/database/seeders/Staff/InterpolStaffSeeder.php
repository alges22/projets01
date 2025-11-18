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
use Ntech\UserPackage\Models\Position;

class InterpolStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffs = [
            [
                "npi" => 1632101074,
                "email" => "glory@example.com",
                "telephone" => "92704478",
                "firstname" => "Glory",
                "lastname" => "Gory",
            ],
        ];
        $village = Village::query()->where('name', 'AGONTINKON')->first();
        foreach ($staffs as $staff){
            $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
            $profileTypeInterpol = ProfileType::where('code', ProfileTypesEnum::interpol->name)->first();
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

            $position = Position::query()->inRandomOrder()->first()->id;
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

            if (!$interpolProfile = $user->profiles()->where('type_id', $profileTypeInterpol->id)->first()) {
                $interpolProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeInterpol->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeInterpol->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeInterpol->id)->first()->institution_id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $interpolProfile->update([
                    'identity_id' => $identity->id,
                ]);
            }

            $this->command->info('Assigning organization to staff');
            $interpolProfile->syncRoles([Roles::INTERPOL]);
        }


    }
}
