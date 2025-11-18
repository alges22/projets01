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
use Ntech\UserPackage\Models\Identity;
use  App\Enums\ProfileTypesEnum;

class ClerkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffs = [
            [
                'email' => 'clerkadmin@simveb.bj',
                'telephone' => '92456000',
                'firstname' => 'Stevy',
                'lastname' => 'LAWSON',
                'npi' => '9076543210',
            ],
        ];

        $village = Village::query()->first();
        $institution = Institution::where('acronym', 'TRICOT')->first();
        $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
        $profileTypeClerk = ProfileType::where('code', ProfileTypesEnum::court->name)->first();

        foreach ($staffs as $staff)
        {
            $clerkIdentity = Identity::updateOrCreate([
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
                'identity_id' => $clerkIdentity->id,
                'password' => Hash::make('here is the pass')
            ]);

            if (!$userProfile = $user->profiles()->where('type_id', $profileTypeUser->id)->first()) {
                Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeUser->id,
                    'identity_id' => $clerkIdentity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeClerk->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeClerk->id)->first()->institution_id,
                ]);
            } else {
                $userProfile->update([
                    'identity_id' => $clerkIdentity->id,
                ]);
            }

            if (!$clerkProfile = $user->profiles()->where('type_id', $profileTypeClerk->id)->first()) {
                $clerkProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeClerk->id,
                    'identity_id' => $clerkIdentity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeClerk->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeClerk->id)->first()->institution_id,
                ]);
            } else {
                $clerkProfile->update([
                    'identity_id' => $clerkIdentity->id,
                ]);
            }
            $clerkProfile->syncRoles(Roles::CLERK);
        }
    }
}
