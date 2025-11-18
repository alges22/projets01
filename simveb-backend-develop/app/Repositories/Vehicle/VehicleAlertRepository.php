<?php

namespace App\Repositories\Vehicle;

use App\Enums\Status;
use App\Http\Resources\AlertTypeResource;
use Exception;
use App\Models\Alert\AlertType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Vehicle\VehicleAlert;
use App\Models\Vehicle\Vehicle;
use App\Repositories\Crud\AbstractCrudRepository;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VehicleAlertRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(VehicleAlert::class);
    }

    /**
     * Checks if the vehicle has alerts
     *
     * @param Vehicle $vehicle
     * @return bool
     */
    public function isVehicleAlerted(Vehicle $vehicle): bool
    {
        $alerts = VehicleAlert::where('vehicle_id', $vehicle->id)
            ->where('status', Status::alerted->name)
            ->get();

        return $alerts->isNotEmpty();

    }

    /**
     *
     */
    public function create()
    {
        return [
            'alert_types' => AlertTypeResource::collection(AlertType::all()),
        ];
    }

    /**
     *
     */
    public function store(array $data, $request = null) : Model | null
    {
        DB::beginTransaction();

        try {
            $vehicle = getVehicleByImmatriculation($data['immatriculation_number']);
            $data['vehicle_id'] = $vehicle->id;
            $data['recorded_at'] = now();
            $data['status'] = Status::alerted->name;
            $alert = parent::store($data);
            $alert->alertTypes()->attach($data['alert_types']);
            DB::commit();

            return $alert;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! L\'alerte n\'a pas pu être enregistrer');
        }
    }

    /**
     *
     */
    function update(Model $model, array $data, $request = null): Model
    {
        DB::beginTransaction();
        try {
            $vehicle = getVehicleByImmatriculation($data['immatriculation_number']);
            $data['vehicle_id'] = $vehicle->id;
            $alert = parent::update($model, $data);
            $alert->alertTypes()->sync($data['alert_types']);

            DB::commit();
            return $alert;
        } catch (Exception $exception)
        {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * Get the alerts of a vehicle group by types of alerts
     *
     * This function searches for all types of alerts on a vehicle and,
     * for each type, returns a list of alerts of that type and
     * concerning that vehicle.
     *
     * @param Vehicle $vehicle
     * @return
     **/
    public function getVehicleAlertsGroupByAlertType(Vehicle $vehicle)
    {
        $alertTypes = AlertType::withWhereHas('vehicleAlerts', function ($query) use ($vehicle) {
            $query->where('vehicle_id', $vehicle->id)->where('status', 'alerted')->latest('vehicle_alerts.created_at');
        })->get();

        return $alertTypes->isNotEmpty() ? $alertTypes : null;
    }
}
