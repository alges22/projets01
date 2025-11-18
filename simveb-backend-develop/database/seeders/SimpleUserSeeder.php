<?php

namespace Database\Seeders;

use App\Enums\ProfileTypesEnum;
use App\Consts\Roles;
use App\Models\Account\User;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use App\Models\Config\Organization;
use App\Models\Config\Village;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ntech\UserPackage\Models\Identity;
use Ntech\UserPackage\Models\Position;
use Ntech\UserPackage\Models\Staff;
use Illuminate\Support\Str;

class SimpleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'wwilliam@gmail.com';
        $npi = '2109876540';
        $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
        $village = Village::query()->where('name','AGONTINKON')->first();

        $identity = Identity::updateOrCreate([
            'npi' => $npi,
        ], [
            'email' => $email,
            'telephone' => '92309876',
            'firstname' => 'William',
            'lastname' => 'WILSON',
            'country_id' => 24,
            'state_id' => $village->district->town->state_id,
            'town_id' => $village->district->town_id,
            'district_id' => $village->district_id,
            'village_id' => $village->id,
        ]);

        $user = User::query()->updateOrCreate([
            'username' => $npi,
        ], [
            'email' => $email,
            'email_verified_at' => now(),
            'identity_id' => $identity->id,
            'password' => Hash::make(Str::random())
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
    }
}
