<?php

namespace App\Services;
use App\Models\Vehicle\Vehicle;
use App\Repositories\MotorcycleRepository;
use App\Services\VehicleService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MotorcycleService
{
    private MotorcycleRepository $repository;
    private VehicleService $vehicleService;

    public function __construct()
    {
        $this->repository = new MotorcycleRepository;
        $this->vehicleService = new VehicleService;
    }

    public function store($data): mixed
    {
        DB::beginTransaction();

        try {
            if (isset($data["vin"])) {
                $vehicle = Vehicle::where('vin', $data["vin"]);
                $data['author_id'] = getOnlineProfile()->id;
                $data['institution_id'] = getOnlineProfile()->institution->id;
                $data['vehicle_id'] = $vehicle?->id;
                $motorcycle = $this->repository->store($data);
            
                DB::commit();
    
                return $motorcycle;
            } else {
                $motorcycles = [];
                $datas = $this->vehicleService->getVehiclesByCustomsReference($data);
                foreach ($datas as $value) {
                    $vehicle = Vehicle::where('vin', $value["vin"]);
                    $value['author_id'] = getOnlineProfile()->id;
                    $value['institution_id'] = getOnlineProfile()->institution->id;
                    $data['vehicle_id'] = $vehicle?->id;
                    $motorcycles[] = $this->repository->store(Arr::only($value, [
                        'institution_id',
                        'author_id',
                        'customs_reference',
                        'vin',
                        'npi',
                        'vehicle_id'
                    ]));
                }
                
                DB::commit();

                return $motorcycles;
            }
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }

    }
}