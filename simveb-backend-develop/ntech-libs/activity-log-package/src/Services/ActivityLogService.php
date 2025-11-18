<?php

namespace Ntech\ActivityLogPackage\Services;

use App\Consts\Roles;
use App\Models\Auth\Profile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Ntech\ActivityLogPackage\Models\NtechActivityLog;
use Ntech\UserPackage\Repositories\UserRepository;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Contracts\Activity;

/***
 * Class ActivityLogService
 * @package Ntech\ServicePackage\Services\ActivityLog
 */
class ActivityLogService
{

    const CREATED = 'CREATED';
    const STAFF_CREATED = 'STAFF_CREATED';
    const UPDATED = 'UPDATED';
    const DELETED = 'DELETED';
    const UPDATE_STAFF = 'UPDATE_STAFF';
    const AUTH = 'AUTH';
    const LOGOUT = 'LOGOUT';
    const LOGIN = 'LOGIN';
    const ATTEMPT_LOGIN = 'ATTEMPT_LOGIN';
    const IMPORTATION = 'IMPORTATION';
    const NEW_USER_INVITED = 'NEW_USER_INVITED';
    /***
     * @var $activityLogRepository
     */
    protected  $activityLogRepository;

    protected $userRepository;

    /***
     * ActivityLogService constructor.
     */
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /***
     * @param $institutionId
     * @param $paginate
     * @return mixed
     */
    public function allActivity($institutionId, $paginate)
    {
        return $this->activityLogRepository->getByInstitution($institutionId, $paginate);
    }


    /***
     * @param $institutionId
     * @return array
     */
    public function getDataForFiltering($institutionId)
    {
        return [
            'log_actions' => $this->getActionsList()
        ];
    }

    /***
     * @param $description
     * @param $logAction
     * @param string $logName
     * @param $causer
     * @param $subject
     * @return ActivityLogger
     */
    public function store(
        $description,
        $logAction,
        $logName = 'default',
        $causer = null,
        $subject = null
    ) {
        $activity = activity();

        if ($causer) {
            $activity = $activity->causedBy($causer);
        }

        if ($subject) {
            $activity = $activity->performedOn($subject);
        }

        $activity->tap(function (Activity $tap) use ($logAction, $logName) {
            $tap->ip_address = request()->ip();
            $tap->log_name = $logName;
            $tap->log_action = $logAction;
        })->log($description);

        return $activity;
    }

    public function getActionsList(): Collection|array
    {
        return [
            ["name" => self::CREATED, "label" => 'Enregistrement'],
            ["name" => self::UPDATED, "label" => 'Modification'],
            ["name" => self::DELETED, "label" => 'Suppression'],
            ["name" => self::AUTH, "label" => 'Authentification'],
            ["name" => self::NEW_USER_INVITED, "label" => "Nouvel uitilisateur invité"],
            ["name" => self::LOGIN, "label" => "Connexion"],
            ["name" => self::LOGOUT, "label" => "Déconnexion"],
            ["name" => self::STAFF_CREATED, "label" => "Création de staff"],
            ["name" => self::UPDATE_STAFF, "label" => "Mise à jour de staff"],
        ];
    }

    public function getCausersList(): array|Collection
    {
        return Profile::query()->whereHas('roles', function ($query) {
            $query->whereIn('name', [Roles::ADMIN, Roles::SERVICE_HEADER, Roles::SERVICE_STAFF]);
        })->select()->with('type', 'identity')->get();
    }


    /***
     * @param $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll($paginate = 15)
    {
        $query = NtechActivityLog::query()->filter();


        if (request()->has('causer_id')) {
            $query = $query->where('causer_id', request()->causer_id);
        }

        if (request()->has('log_action')) {
            $query = $query->where('log_action', request()->log_action);
        }

        if (request()->has('date_start') && request()->has('date_end')) {
            $query = $query->whereBetween(
                'created_at',
                [Carbon::parse(request()->date_start)->startOfDay(), Carbon::parse(request()->date_end)->endOfDay()]
            );
        }


        return $query->latest()->paginate($paginate);
    }
    public function get($id)
    {
        $query = NtechActivityLog::with('subject')->find($id);


        return $query;
    }

    /**
     * @param $user_id
     * @param $action_type
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getLastLogByUserAndAction($user_id, $action_type)
    {
        return NtechActivityLog::query()
            ->where("causer_id", $user_id)
            ->where('log_action', $action_type)
            ->orderByDesc('created_at')
            ->first();
    }

    public function getLastLogsByProfile($causer_id, $count = 3)
    {
        return NtechActivityLog::query()
            ->where("causer_id", $causer_id)
            ->orderByDesc('created_at')
            ->limit($count)
            ->get();
    }
}
