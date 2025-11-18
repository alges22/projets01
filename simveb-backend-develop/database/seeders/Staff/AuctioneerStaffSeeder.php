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

class AuctioneerStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffs = [
            [
                "npi" => 4321998760,
                "email" => "benjamin@example.com",
                "telephone" => "91230987",
                "firstname" => "Benjamin",
                "lastname" => "MARTIN",
            ],
        ];
        $village = Village::query()->where('name', 'AGONTINKON')->first();
        foreach ($staffs as $staff){
            $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
            $profileTypeInterpol = ProfileType::where('code', ProfileTypesEnum::auctioneer->name)->first();
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

            if (!$auctioneerProfile = $user->profiles()->where('type_id', $profileTypeInterpol->id)->first()) {
                $auctioneerProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeInterpol->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeInterpol->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeInterpol->id)->first()->institution_id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $auctioneerProfile->update([
                    'identity_id' => $identity->id,
                ]);
            }

            $this->command->info('Assigning organization to staff');
            $auctioneerProfile->syncRoles([Roles::AUCTIONEER]);
        }


    }
}
