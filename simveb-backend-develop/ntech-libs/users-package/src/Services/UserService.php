<?php

namespace Ntech\UserPackage\Services;

use App\Consts\Roles;
use App\Models\Auth\EmailVerification;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Carbon;
use Ntech\ActivityLogPackage\Services\ActivityLogService;
use Ntech\UserPackage\Models\Identity;
use Ntech\UserPackage\Repositories\IdentityRepository;
use Ntech\UserPackage\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserService
{
    private readonly UserRepository $userRepository;
    private readonly IdentityRepository $identityRepository;
    private readonly ActivityLogService $activityLogService;
    public function __construct()
    {
        $this->userRepository = new UserRepository;
        $this->identityRepository = new IdentityRepository;
        $this->activityLogService = new ActivityLogService;
    }

    public function create($data, $sendVerificationMail = false)
    {

        $identity = $this->identityRepository->create($data);
        $data['identity_id'] = $identity->id;

        $user =  $this->userRepository->create($data);

        if ($sendVerificationMail)
        {
            $this->sendVerificationToken($user);
        }

        return $user;
    }

    public function update($user, $data)
    {
        $this->identityRepository->update($user->identity, $data);

        return $this->userRepository->update($user, $data);
    }

    public function sendVerificationToken($user)
    {
        $user->notify(new CustomVerifyEmail());
    }

    public function verifyUser($token)
    {

        $verification = EmailVerification::query()->where('token',$token)->first();

        if ($verification && Carbon::parse($verification->expire_at)->isFuture())
        {
            $user = $this->userRepository->getByEmail($verification->email);
            $user->update(['email_verified_at' => now()]);
            $user->assignRole([Roles::VEHICLE_OWNER]);
            $verification->delete();

            return ['status' => 200, 'message' => "Email vérifié avec succès"];
        }else
        {
            $verification?->delete();

            $message = $verification == null ? 'Email de vérification invalide' : 'Lien de vérification expiré';
            abort(ResponseAlias::HTTP_BAD_REQUEST,$message);
        }
    }

    public function getUserDetails($npi)
    {

        $identity  = Identity::where('npi', $npi)->first();
        if (!$identity) {
            throw new \Exception("Erreur: Désolé cet utilisateur n'existe pas.", ResponseAlias::HTTP_NOT_FOUND);
        }
        $user = $identity->user()
            ->with([
                'identity:id,firstname,lastname,npi,email,telephone',
                'profiles',
                'profiles.type:id,name,role_id',
                'profiles.type.role',
                'profiles.institution:id,name,email',
                'profiles.vehicleOwner.vehicles:id,vin,owner_id',
                'profiles.vehicleOwner.vehicles.immatriculation:id,number_label,vehicle_id',
            ])->first();

            for ($i=0; $i < $user->profiles->count(); $i++) { 
                $user->profiles[$i]->latest_activity_logs = $this->activityLogService->getLastLogsByProfile($user->profiles[$i]->id);
            }

        return $user;
    }

}
