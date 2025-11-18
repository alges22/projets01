<?php

namespace Database\Seeders;

use App\Consts\Roles;
use App\Enums\SpaceTypesEnum;
use App\Enums\ProfileTypesEnum;
use App\Models\Account\User;
use App\Models\Space\Space;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ntech\UserPackage\Models\Identity;

class ApprovedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profileTypeApproved = ProfileType::where('code', ProfileTypesEnum::approved->name)->first();
        $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
        $members = [
            [
                'email' => 'nautilustest@mail.com',
                'telephone' => !in_array(app()->env, ['local', 'dev', 'development', 'staging']) ? '51104856' : '64000001',
                'firstname' => 'Admin',
                'lastname' => 'Admin',
                'npi' => !in_array(app()->env, ['local', 'dev', 'development', 'staging']) ? '4811676017' : '8765432101',
                'roles' => [Roles::APPROVED_ADMIN],
            ],
        ];

        foreach ($members as $member) {
            $identity = Identity::updateOrCreate([
                'email' => $member['email'],
            ], [
                'npi' => $member['npi'],
                'telephone' => '+229' . $member['telephone'],
                'firstname' => $member['firstname'],
                'lastname' => $member['lastname'],
            ]);

            $user = User::updateOrCreate([
                'email' => $member['email'],
            ], [
                'username' => $member['npi'],
                'email_verified_at' => now(),
                'identity_id' => $identity->id,
            ]);

            if (!$userProfile = $user->profiles()->where('type_id', $profileTypeUser->id)->first()) {
                $userProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeUser->id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $userProfile->update([
                    'identity_id' => $identity->id,
                ]);
            }

            if (!$approvedProfile = $user->profiles()->where('type_id', $profileTypeApproved->id)->first()) {
                $approvedProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeApproved->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeApproved->id)->first()->id,
                    'institution_id' => Space::where('profile_type_id', $profileTypeApproved->id)->first()->institution_id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $approvedProfile->update([
                    'identity_id' => $identity->id,
                    'space_id' => $approvedProfile->space_id ?: Space::where('profile_type_id', $profileTypeApproved->id)->first()->id,
                    'institution_id' => $approvedProfile->institution_id ?: Space::where('profile_type_id', $profileTypeApproved->id)->first()->institution_id,
                ]);
            }
            $approvedProfile->syncRoles($member['roles']);
        }
    }
}
