<?php

namespace Database\Seeders\Staff;

use App\Consts\Roles;
use App\Enums\SpaceTypesEnum;
use App\Enums\ProfileTypesEnum;
use App\Models\Account\User;
use App\Models\Config\Village;
use App\Models\Institution\Institution;
use App\Models\Space\Space;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ntech\UserPackage\Models\Identity;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffs = [
            [
                'email' => 'contact@lesbagnoles.com',
                'telephone' => '82654091',
                'firstname' => 'Sophia',
                'lastname' => 'MOORE',
                'npi' => '1098765430',
            ]
        ];


        $village = Village::query()->first();
        $institution = Institution::query()->with('town')->first();
        $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
        $profileTypeDistributor = ProfileType::where('code', ProfileTypesEnum::distributor->name)->first();

        foreach ($staffs as $staff) {
            $identityDistributor = Identity::updateOrCreate([
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
                'identity_id' => $identityDistributor->id,
                'password' => Hash::make('here is the pass'),
            ]);

            if (!$userProfile = $user->profiles()->where('type_id', $profileTypeUser->id)->first()) {
                Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeUser->id,
                    'identity_id' => $identityDistributor->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeDistributor->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeDistributor->id)->first()->institution_id,
                ]);
            } else {
                $userProfile->update([
                    'identity_id' => $identityDistributor->id,
                ]);
            }

            if (!$distributorProfile = $user->profiles()->where('type_id', $profileTypeDistributor->id)->first()) {
                $distributorProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeDistributor->id,
                    'identity_id' => $identityDistributor->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeDistributor->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeDistributor->id)->first()->institution_id,
                ]);
            } else {
                $distributorProfile->update([
                    'identity_id' => $identityDistributor->id,
                ]);
            }
            $distributorProfile->syncRoles(Roles::DISTRIBUTOR);
        }
    }

}
