<?php

namespace App\Repositories;

use App\Consts\AvailableServiceType;
use App\Consts\Roles;
use App\Enums\Status;
use App\Models\Action;
use App\Models\SaleDeclaration;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Traits\UserDataTrait;
use Illuminate\Support\Facades\Auth;

class ActionRepository extends AbstractCrudRepository
{
    use UserDataTrait;

    public function __construct()
    {
        parent::__construct(Action::class);
    }

    public function getAll(bool $paginate = true,$relations = []): mixed
    {
        $query = $this->model->
        newQuery()
            ->with([
                'permissionService',
                'permissionService.permission',
                'serviceStep'
            ])->orderByDesc('position');

        return $query->filter()->paginate(request()->input('per_page',15));
    }
}
