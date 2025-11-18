<?php

namespace App\Services;

use App\Consts\NotificationNames;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Exceptions\UnexceptedErrorException;
use App\Models\Space\Space;
use App\Models\Auth\Invitation;
use App\Models\Auth\Profile;
use App\Models\PoliceOfficer\PoliceOfficer;
use App\Models\Vehicle\GovVehicle;
use App\Notifications\NotificationSender;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Ntech\UserPackage\Models\Staff;

class InvitationService
{
    public function createData()
    {
        $profile = getOnlineProfile();
        $roles = $profile->type->roles()->select('id', 'label')->get();

        if ($profile->type->code === ProfileTypesEnum::court->name) {
            $roles = $profile->roles()->select('id', 'label')->get();
        }

        return [
            'roles' => $roles
        ];
    }

    public function getAll($paginate = true)
    {
        $onlineProfile = auth()->user()->onlineProfile;
        $space = $onlineProfile->space;

        $query = Invitation::when($space, function ($q) use ($space) {
            $q->where('space_id', $space->id);
        })
            ->where('profile_type_id', $onlineProfile->type_id)
            ->filter()
            ->with(['space:id,institution_id,profile_type_id', 'space.institution:id,name,logo_path', 'space.profileType:id,code,name', 'profileType:id,code,name', 'author:id,identity_id', 'author.identity:id,firstname,lastname,email', 'roles:id,label']);

        return $paginate ? $query->paginate(request('per_page', 20)) : $query->get();
    }

    /**
     * @param array $data
     * @param array $roles
     * @return array
     */
    public function store(array $data, array $roles = []): array
    {
        try {
            $retrievePerson = (new IdentityService)->showByNpi($data['npi'])->response()->getData(true)['data'];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error($e);
            throw new UnexceptedErrorException;
        }

        DB::beginTransaction();
        try {
            $rolesId = !empty($roles) ? $roles : Arr::pull($data, 'roles');
            unset($data['roles']);

            $authOnlineProfile = getOnlineProfile();
            $invitation = Invitation::create([
                'npi' => $data['npi'],
                'space_id' => $data['space_id'] ?? $authOnlineProfile->space_id,
                'profile_type_id' => $data['profile_type_id'] ?? $authOnlineProfile->type_id,
                'author_id' => $authOnlineProfile->id,
                'email' => $retrievePerson['email'],
                'telephone' => $retrievePerson['telephone'],
                'status' => Status::pending->name,
            ]);

            $invitation->syncRoles($rolesId);

            sendMail(
                $invitation->email,
                null,
                NotificationNames::SPACE_MEMBER_INVITATION,
                [
                    'space_name' => $invitation->space ? $invitation->space->institution?->name : $authOnlineProfile->space->institution?->name,
                ]
            );

            DB::commit();
            return [true, $invitation->load(['roles:id,label'])];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(500, $exception->getMessage());
        }
    }

    public function validateInvitation(Invitation $invitation)
    {
        $user = auth()->user();

        if ($invitation->npi != $user->username) {
            return [false, ['message' => "Action non autorisée.", 'code' => Response::HTTP_FORBIDDEN]];
        }

        if ($invitation->accepted || $invitation->denied) {
            return [false, ['message' => "Impossible d'exécuter cette action à ce stade", 'code' => Response::HTTP_UNPROCESSABLE_ENTITY]];
        }

        DB::beginTransaction();
        try {
            $invitation->update(['accepted' => true, 'status' => Status::validated->name]);

            $rolesToAssign = [];

            if ($invitation->space) {
                $actualMembers = $invitation->space->profiles->count();
            }

            $profile = Profile::create([
                'user_id' => $user->id,
                'type_id' => $invitation->profile_type_id,
                'space_id' => $invitation->space_id,
                'institution_id' => $invitation->space->institution_id,
                'identity_id' => $user->identity_id,
            ]);

            if ($invitation->roles) {
                $rolesToAssign += $invitation->roles()->pluck('name')->toArray();
            }

            $profile->assignRole(array_unique($rolesToAssign));

            if ($profile->type->code == ProfileTypesEnum::anatt->name) {
                Staff::query()
                    ->where('invitation_id', $invitation->id)
                    ->update([
                        'profile_id' => $profile->id,
                        'identity_id' => $user->identity_id,
                    ]);
            }
            if (
                $profile->type->code == ProfileTypesEnum::central_garage->name && $vehicle = GovVehicle::query()
                ->where('owner_npi', $invitation->npi)
                ->whereNull('profile_id')
                ->first()
            ) {
                $vehicle->update(['profile_id' => $vehicle->id]);
            }
            if ($profile->type->code == ProfileTypesEnum::police->name) {
                PoliceOfficer::create([
                    'profile_id' => $profile->id,
                    'identity_id' => $user->identity_id
                ]);
            }

            DB::commit();
            return [true, $invitation];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(500, $exception->getMessage());
        }
    }

    public function deny(Invitation $invitation)
    {
        $user = auth()->user();
        if ($invitation->npi != $user->username) {
            return [false, ['message' => "Action non autorisée.", 'code' => Response::HTTP_FORBIDDEN]];
        }
        $invitation->update(['denied' => true, 'status' => Status::rejected->name]);

        return [true, $invitation];
    }

    public function resend(Invitation $invitation)
    {
        if ($invitation->status == Status::pending->name) {
                sendMail(
                    $invitation->email,
                    null,
                    NotificationNames::SPACE_MEMBER_INVITATION,
                    [
                        'space_name' => $invitation->space ? $invitation->space->institution?->name : getOnlineProfile()->space->institution?->name,
                    ]
                );

            return [true, ['message' => 'Invitation renvoyée avec succès.']];
        }

        return [false, ['message' => 'Impossible de renvoyer une invitation déjà acceptée ou refusée.', 'code' => Response::HTTP_FORBIDDEN]];
    }
}
