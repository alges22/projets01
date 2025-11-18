<?php

namespace Database\Seeders;

use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;
use App\Models\Account\User;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ntech\UserPackage\Models\Identity;

class InterpolMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
        $profileTypeInterpol = ProfileType::where('code', ProfileTypesEnum::interpol->name)->first();

        foreach ($this->getMembers() as $member) {
            $identity = Identity::updateOrCreate([
                'email' => $member['email'],
                'npi' => $member['npi'],
            ], [
                'telephone' => '+229' . $member['telephone'],
                'firstname' => $member['firstname'],
                'lastname' => $member['lastname'],
            ]);

            $user = User::updateOrCreate([
                'username' => $member['npi'],
                'email' => $member['email'],
            ], [
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

            if (!$interpolPorfile = $user->profiles()->where('type_id', $profileTypeInterpol->id)->first()) {
                $interpolPorfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeInterpol->id,
                    'identity_id' => $identity->id,
                ]);
            } else {
                $interpolPorfile->update([
                    'identity_id' => $identity->id,
                ]);
            }
            $interpolPorfile->syncRoles($member['roles']);
        }
    }

    public function getMembers()
    {
        return [
            [
                'email' => 'interpol@simveb.bj',
                'telephone' => '61657075',
                'firstname' => 'Ava',
                'lastname' => 'HALL',
                'npi' => '0987654320',
                'roles' => [Roles::SPACE_HEADER],
            ],
            [
                'email' => 'nautilustest@mail.com',
                'telephone' => '64000001',
                'firstname' => 'Admin',
                'lastname' => 'Admin',
                'npi' => '8765432101',
                'roles' => [Roles::ADMIN],
            ],
        ];
    }
}
