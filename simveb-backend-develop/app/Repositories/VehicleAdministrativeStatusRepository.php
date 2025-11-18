<?php

namespace App\Repositories;

use App\Consts\Roles;
use App\Enums\Status;
use App\Models\Account\Declarant;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleAdministrativeStatus;
use App\Repositories\Crud\AbstractCrudRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ntech\UserPackage\Models\Identity;

class VehicleAdministrativeStatusRepository extends AbstractCrudRepository
{
    public function __construct()
    {
      parent::__construct(VehicleAdministrativeStatus::class);
    }

    public function searchDeclarant()
    {
        $npiOrIfu = request()->input('npiOrIfu');
        $identityId = Identity::where('npi', $npiOrIfu)
            ->orWhere('ifu', $npiOrIfu)
            ->pluck('id')
            ->first();
        $declarant = null;
        if ($identityId)
        {
            $declarant = Declarant::select(['id'])->where('identity_id', $identityId)->first();
        }
        return ['declarant_id' => $declarant];
    }

    public function searchVehicleAndOwner()
    {
        $chassisNumber = strtolower(request()->input('chassisNumber'));
        $vehicleId = Vehicle::with('owner')
            ->where('vin', $chassisNumber)->pluck('id')->first();

        return ['vehicle_id' => $vehicleId,];
    }

    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        $query = $this->model->newQuery()
            ->select()
            ->where('status', '<>', Status::pending->name)
            ->with($relations)
            ->orderByDesc('created_at')
            ->filter();

        if (Auth::user()->hasRole([Roles::SERVICE_HEADER])){
            $query =  $query->whereHas('activeTreatment',
                fn(Builder $query) =>
                $query->where('service_id',$this->staff()->headService->id)
            );
        }
        if (Auth::user()->hasRole([Roles::SERVICE_STAFF])){
            $query =  $query->whereHas('activeTreatment',
                fn(Builder $query) =>
                $query->whereIn('service_id',$this->staff()->services()->pluck('id')->toArray())
                    ->orWhere('responsible_id',Auth::id())
            );
        }

        return $query->paginate(request()->input('per_page',15));
    }

    public function anattValidation()
    {
        try {
            DB::beginTransaction();
        }catch (Exception $exception){

        }
    }

}
