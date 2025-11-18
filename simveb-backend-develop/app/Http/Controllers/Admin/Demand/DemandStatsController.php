<?php

namespace App\Http\Controllers\Admin\Demand;

use App\Http\Controllers\Controller;
use App\Repositories\Demand\DemandRepository;

class DemandStatsController extends Controller
{
    public function __construct(private readonly DemandRepository $repository)
    {
        //
    }

    /**
     *
     */
    public function total()
    {
        return response($this->repository->getAllDemandsTotal());
    }

    /**
     *
     */
    public function totalByService()
    {
        return response($this->repository->getTotalDemandByService());
    }

    /**
     *
     */
    public function totalByVehicleCategory()
    {
        return response($this->repository->getVehicleCategoryDemandsTotal());
    }

    /**
     *
     */
    public function overdue()
    {
        // return response($this->repository->getOverdueDemands());
    }
}
