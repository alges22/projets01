<?php

namespace App\Repositories;

use App\Consts\AvailableServiceType;
use App\Consts\Roles;
use App\Enums\Status;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Mutation;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Traits\UserDataTrait;
use Illuminate\Support\Facades\Auth;

class MutationRepository extends AbstractCrudRepository
{
    use UserDataTrait;

    public function __construct()
    {
        parent::__construct(AvailableServiceType::MUTATION);
    }

    public function getAll(bool $paginate = true,$relations = []): mixed
    {
        $query = $this->model->
        newQuery()
            ->where('status','<>',Status::pending->name)
            ->with([
                'saleDeclaration.vehicleOwner.identity:id,lastname,firstname,email,telephone',
            ])->orderByDesc('created_at');

        if (Auth::user()->hasRole([Roles::SERVICE_HEADER])){
            $query =  $query->whereHas('activeTreatment',
                fn($query) => $query->whereIn('service_id',$this->staff()->services()->pluck('id')->toArray()));
        }
        if (Auth::user()->hasRole([Roles::SERVICE_STAFF])){
            $query =  $query->whereHas('activeTreatment',
                fn($query) => $query->where('responsible_id',Auth::id()));
        }

        return $query->paginate(request()->input('per_page',15));
    }
}
