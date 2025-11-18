<?php

namespace Database\Seeders\Staff;

use App\Consts\Roles;
use App\Enums\Status;
use App\Models\Auth\Invitation;
use App\Models\Institution\Institution;
use App\Models\Space\Space;
use App\Models\Account\User;
use App\Models\Auth\Profile;
use App\Models\Config\Village;
use App\Enums\ProfileTypesEnum;
use App\Services\InvitationService;
use Illuminate\Database\Seeder;
use App\Models\Auth\ProfileType;
use App\Models\Config\Organization;
use Ntech\UserPackage\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Ntech\UserPackage\Models\Identity;
use Ntech\UserPackage\Models\Position;

class AnattAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'nautilustest@mail.com';
        $npi = !in_array(app()->env, ['local', 'dev', 'development', 'staging']) ? '4811676017' : '8765432101';
        $profileTypeUser = ProfileType::where('code', ProfileTypesEnum::user->name)->first();
        $profileTypeCG = ProfileType::where('code', ProfileTypesEnum::central_garage->name)->first();
        $profileTypeGMD = ProfileType::where('code', ProfileTypesEnum::gmd->name)->first();
        $profileTypeAuctioneer = ProfileType::where('code', ProfileTypesEnum::auctioneer->name)->first();
        $profileTypeAnatt = ProfileType::where('code', ProfileTypesEnum::anatt->name)->first();
        $profileTypePolice = ProfileType::where('code', ProfileTypesEnum::police->name)->first();
        $profileTypeGMA = ProfileType::where('code', ProfileTypesEnum::gma->name)->first();
        $profileTypeBank = ProfileType::where('code', ProfileTypesEnum::bank->name)->first();
        $profileTypeCourt = ProfileType::where('code', ProfileTypesEnum::court->name)->first();
        $profileTypeDistributor = ProfileType::where('code', ProfileTypesEnum::distributor->name)->first();
        $profileTypeAffiliate = ProfileType::where('code', ProfileTypesEnum::affiliate->name)->first();
        $profileTypeInterpol = ProfileType::where('code', ProfileTypesEnum::interpol->name)->first();
        $village = Village::query()->where('name', 'AGONTINKON')->first();

        $identity = Identity::updateOrCreate([
            'email' => $email,
        ], [
            'npi' => $npi,
            'telephone' => in_array(app()->env, ['local', 'dev', 'development', 'staging']) ? '+22951104856' : '+22964000001',
            'firstname' => 'Admin',
            'lastname' => 'Admin',
            'country_id' => 24,
            'state_id' => $village->district->town->state_id,
            'town_id' => $village->district->town_id,
            'district_id' => $village->district_id,
            'village_id' => $village->id,
        ]);


        $position = Position::first()->id;

        $user = User::query()->updateOrCreate([
            'email' => $email,
        ], [
            'username' => $npi,
            'email_verified_at' => now(),
            'identity_id' => $identity->id,
            'password' => Hash::make('here is the pass')
        ]);


        if (!$userProfile = $user->profiles()->where('type_id', $profileTypeUser->id)->first()) {
            $userProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeUser->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeUser->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeUser->id)->first()?->institution_id,
            ]);
        } else {
            $userProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeUser->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeUser->id)->first()?->institution_id,
            ]);
        }

        if (!$cgProfile = $user->profiles()->where('type_id', $profileTypeCG->id)->first()) {
            $cgProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeCG->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeCG->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeCG->id)->first()?->institution_id,
            ]);
            /* Invitation::create([
                'npi' => $npi,
                'space_id' => Space::where('profile_type_id', $profileTypeUser->id)->first()?->id,
                'profile_type_id' => $profileTypeCG->id,
                'author_id' => $userProfile->id,
                'email' => $email,
                'telephone' => '+229' . '64000001',
                'status' => Status::pending->name,
            ]);
            (new InvitationService)->store([
                'npi' =>  $npi,
                'space_id' => Space::where('profile_type_id', $profileTypeUser->id)->first()?->id,
                'profile_type_id' => Space::where('profile_type_id', $profileTypeUser->id)->first()?->profile_type_id,
                'roles' => Space::where('profile_type_id', $profileTypeUser->id)->first()?->profileType->roles()->pluck('id')->toArray()
            ]); */
        } else {
            $cgProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeCG->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeCG->id)->first()?->institution_id,
            ]);
        }

        if (!$anattProfile = $user->profiles()->where('type_id', $profileTypeAnatt->id)->first()) {
            $anattProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeAnatt->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeAnatt->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeAnatt->id)->first()?->institution_id,
            ]);
        } else {
            $anattProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeAnatt->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeAnatt->id)->first()?->institution_id,
            ]);
        }

        if (!$auctioneerProfile = $user->profiles()->where('type_id', $profileTypeAuctioneer->id)->first()) {
            $auctioneerProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeAuctioneer->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeAuctioneer->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeAuctioneer->id)->first()?->institution_id,
            ]);
        } else {
            $auctioneerProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeAuctioneer->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeAuctioneer->id)->first()?->institution_id,
            ]);
        }

        if (!$policeProfile = $user->profiles()->where('type_id', $profileTypePolice->id)->first()) {
            $policeProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypePolice->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypePolice->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypePolice->id)->first()?->institution_id,
            ]);
        } else {
            $policeProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypePolice->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypePolice->id)->first()?->institution_id,
            ]);
        }

        if (!$gmaProfile = $user->profiles()->where('type_id', $profileTypeGMA->id)->first()) {
            $gmaProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeGMA->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeGMA->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeGMA->id)->first()?->institution_id,
            ]);
        } else {
            $gmaProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeGMA->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeGMA->id)->first()?->institution_id,
            ]);
        }

        if (!$gmdProfile = $user->profiles()->where('type_id', $profileTypeGMD->id)->first()) {
            $gmdProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeGMD->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeGMD->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeGMD->id)->first()?->institution_id,
            ]);
        } else {
            $gmdProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeGMD->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeGMD->id)->first()?->institution_id,
            ]);
        }

        if (!$bankProfile = $user->profiles()->where('type_id', $profileTypeBank->id)->first()) {
            $bankProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeBank->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeBank->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeBank->id)->first()?->institution_id,
            ]);
        } else {
            $bankProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeBank->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeBank->id)->first()?->institution_id,
            ]);
        }

        /*if (!$clerkProfile = $user->profiles()->where('type_id', $profileTypeClerk->id)->first()) {
            $clerkProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeClerk->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeClerk->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeClerk->id)->first()?->institution_id,
            ]);
        } else {
            $clerkProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeClerk->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeClerk->id)->first()?->institution_id,
            ]);

        $courtInstitutions = Institution::query()->where('profile_type_code', ProfileTypesEnum::court->name)->get();

        foreach ($courtInstitutions as $key => $institution) {

            if (!$clerkProfile = $user->profiles()->where([['type_id', $profileTypeCourt->id],['institution_id', $institution->id]])->whereHas('roles', fn($query) => $query->where('name', Roles::CLERK))->first())
            {
                $clerkProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeCourt->id,
                    'identity_id' => $identity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeCourt->id)->first()?->id,
                    'institution_id' => Space::where([['profile_type_id', $profileTypeCourt->id],['institution_id', $institution->id]])->first()?->institution_id,
                ]);

                $clerkProfile->syncRoles([Roles::CLERK]);
            }else{
                $clerkProfile->update([
                    'identity_id' => $identity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeCourt->id)->first()?->id,
                    'institution_id' => Space::where([['profile_type_id', $profileTypeCourt->id],['institution_id', $institution->id]])->first()?->institution_id,
                ]);
            }


            if (!$judgeProfile = $user->profiles()->where([['type_id', $profileTypeCourt->id],['institution_id', $institution->id]])->whereHas('roles', fn($query) => $query->where('name', Roles::INVESTIGATING_JUDGE))->first())
            {
                $judgeProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeCourt->id,
                    'identity_id' => $identity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeCourt->id)->first()?->id,
                    'institution_id' => Space::where([['profile_type_id', $profileTypeCourt->id],['institution_id', $institution->id]])->first()?->institution_id,
                ]);

                $judgeProfile->syncRoles([Roles::INVESTIGATING_JUDGE]);
            } else {
                $judgeProfile->update([
                    'identity_id' => $identity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeCourt->id)->first()?->id,
                    'institution_id' => Space::where([['profile_type_id', $profileTypeCourt->id],['institution_id', $institution->id]])->first()?->institution_id,
                ]);
            }
        }


        /*if (!$judgeProfile = $user->profiles()->where('type_id', $profileTypeJudge->id)->first()) {
            $judgeProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeJudge->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeJudge->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeJudge->id)->first()?->institution_id,
            ]);
        } else {
            $judgeProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeJudge->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeJudge->id)->first()?->institution_id,
            ]);
        }*/

        $courtInstitutions = Institution::query()->where('profile_type_code', ProfileTypesEnum::court->name)->get();

        foreach ($courtInstitutions as $key => $institution) {

            if (!$clerkProfile = $user->profiles()->where([['type_id', $profileTypeCourt->id], ['institution_id', $institution->id]])->whereHas('roles', fn($query) => $query->where('name', Roles::CLERK))->first()) {
                $clerkProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeCourt->id,
                    'identity_id' => $identity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeCourt->id)->first()?->id,
                    'institution_id' => Space::where([['profile_type_id', $profileTypeCourt->id], ['institution_id', $institution->id]])->first()?->institution_id,
                ]);

                $clerkProfile->syncRoles([Roles::CLERK]);
            } else {
                $clerkProfile->update([
                    'identity_id' => $identity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeCourt->id)->first()?->id,
                    'institution_id' => Space::where([['profile_type_id', $profileTypeCourt->id], ['institution_id', $institution->id]])->first()?->institution_id,
                ]);
            }


            if (!$judgeProfile = $user->profiles()->where([['type_id', $profileTypeCourt->id], ['institution_id', $institution->id]])->whereHas('roles', fn($query) => $query->where('name', Roles::INVESTIGATING_JUDGE))->first()) {
                $judgeProfile = Profile::create([
                    'user_id' => $user->id,
                    'type_id' => $profileTypeCourt->id,
                    'identity_id' => $identity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeCourt->id)->first()?->id,
                    'institution_id' => Space::where([['profile_type_id', $profileTypeCourt->id], ['institution_id', $institution->id]])->first()?->institution_id,
                ]);

                $judgeProfile->syncRoles([Roles::INVESTIGATING_JUDGE]);
            } else {
                $judgeProfile->update([
                    'identity_id' => $identity->id,
                    'space_id' => Space::where('profile_type_id', $profileTypeCourt->id)->first()?->id,
                    'institution_id' => Space::where([['profile_type_id', $profileTypeCourt->id], ['institution_id', $institution->id]])->first()?->institution_id,
                ]);
            }
        }

        if (!$distributorProfile = $user->profiles()->where('type_id', $profileTypeDistributor->id)->first()) {
            $distributorProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeDistributor->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeDistributor->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeDistributor->id)->first()?->institution_id,
            ]);
        } else {
            $distributorProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeDistributor->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeDistributor->id)->first()?->institution_id,
            ]);
        }

        if (!$interpolProfile = $user->profiles()->where('type_id', $profileTypeInterpol->id)->first()) {
            $interpolProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeInterpol->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeInterpol->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeInterpol->id)->first()?->institution_id,
            ]);
        } else {
            $interpolProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeInterpol->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeInterpol->id)->first()?->institution_id,
            ]);
        }

        if (!$affiliateProfile = $user->profiles()->where('type_id', $profileTypeAffiliate->id)->first()) {
            $affiliateProfile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $profileTypeAffiliate->id,
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeAffiliate->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeAffiliate->id)->first()?->institution_id,
            ]);
        } else {
            $affiliateProfile->update([
                'identity_id' => $identity->id,
                'space_id' => Space::where('profile_type_id', $profileTypeAffiliate->id)->first()?->id,
                'institution_id' => Space::where('profile_type_id', $profileTypeAffiliate->id)->first()?->institution_id,
            ]);
        }

        $staff = Staff::query()->updateOrCreate(
            [
                'position_id' => $position,
                'identity_id' => $identity->id,
                "profile_id" => $anattProfile->id
            ]
        );

        $this->command->info('Assigning organization to staff');
        $staff->organizations()->sync(Organization::query()->pluck('id')->toArray());
        $anattProfile->syncRoles([Roles::ADMIN, Roles::DEMAND_MANAGER]);
        $policeProfile->syncRoles([Roles::POLICE_ADMIN]);
        $cgProfile->syncRoles([Roles::CG_ADMIN]);
        $gmaProfile->syncRoles([Roles::GMA_ADMIN]);
        $gmdProfile->syncRoles([Roles::GMD_ADMIN]);
        $auctioneerProfile->syncRoles([Roles::AUCTIONEER]);
        $bankProfile->syncRoles([Roles::BANK]);
        $distributorProfile->syncRoles([Roles::DISTRIBUTOR]);
        $affiliateProfile->syncRoles([Roles::AFFILIATE_ADMIN]);
        $interpolProfile->syncRoles([Roles::INTERPOL]);
    }
}
