<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImmatriculationResource;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Order\Demand;
use App\Models\Vehicle\Vehicle;
use App\Repositories\Immatriculation\ImmatriculationRepository;

class ImmatriculationController extends Controller
{
    public function __construct(private readonly ImmatriculationRepository $immatriculationRepository,)
    {}

    public function searchImmatriculation()
    {
        return response($this->immatriculationRepository->search());
    }

    public function showDemand(Demand $immatriculationDemand)
    {
        return response($immatriculationDemand->load($immatriculationDemand::relations()));
    }

    public function showImmatriculation($vin)
    {
        $model = Vehicle::where("vin", $vin)->firstOrFail()->load('immatriculation','immatriculation.plates');
        
        return response($model->immatriculation);
    }
}
