<?php
namespace App\Traits\Demands;

use App\Http\Requests\Demand\GetDemandRules;
use App\Models\Order\Demand;
use App\Repositories\Crud\CrudRepository;
use App\Repositories\Demand\DemandRepository;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\ValidVINRule;
use App\Services\VehicleService;
use Illuminate\Support\Facades\Validator;

trait ValidateDemandTrait
{

    protected function validateDemand(array $data)
    {
        $repository = new DemandRepository;
        $vehicle = (new VehicleService)->getVehicleByVinOrImmatriculation(['vin' => $data['vin']]);
        $demand = $repository->findDemandByService($data['service_id'], $vehicle?->id);

        Validator::validate($data,GetDemandRules::getDemandRuleByService($demand->service, true));

        return $demand;
    }
}
