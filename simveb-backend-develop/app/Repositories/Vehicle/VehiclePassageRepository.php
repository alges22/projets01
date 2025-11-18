<?php

namespace App\Repositories\Vehicle;

use Exception;
use App\Enums\VehiclePassageType;
use App\Enums\VehicleTypeAtBorder;
use App\Http\Resources\AlertTypeResource;
use App\Http\Resources\ClientVehicleResource;
use App\Http\Resources\VehicleOwnerResource;
use App\Models\Vehicle\VehiclePassage;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Services\VehiclePassageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VehiclePassageRepository extends AbstractCrudRepository
{

    public function __construct(private readonly VehiclePassageService $passageService)
    {
        parent::__construct(VehiclePassage::class);
    }

    /**
     * @return array
     */
    public function create(): array
    {
        return [
            "countries" => DB::table('countries')->select(["id", "name", "iso2", "phonecode", "emoji", "emojiu"])->get(),
            "passage_types" => VehiclePassageType::toNameValueWithKey(),
            "vehicle_types" => VehicleTypeAtBorder::toNameValueWithKey(),
        ];
    }

    /**
     * @return Model|null
     *
     */
    public function store(array $data, $request = null): Model | null
    {
        DB::beginTransaction();

        try {
            $data['driving_license_photo'] = $this->passageService->getStoredFile($data['driving_license_file'], 'vehicle-passages-files');
            if (Arr::exists($data, 'gray_card_file'))
            {
                $data['gray_card_photo'] = $this->passageService->getStoredFile($data['gray_card_file'], 'vehicle-passages-files');
            }
            $model = parent::store($data);
            DB::commit();

            return $model;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * @return Model
     */
    function update(Model $vehiclePassage, array $data, $request = null): Model
    {
        DB::beginTransaction();

        try {
            $data['driving_license_photo'] = $this->passageService->getStoredFile($data['driving_license_file'], 'vehicle-passages-files');
            if (Arr::exists($data, 'gray_card_file'))
            {
                $data['gray_card_photo'] = $this->passageService->getStoredFile($data['gray_card_file'], 'vehicle-passages-files');
            }
            $vehiclePassage = parent::update($vehiclePassage, $data);
            DB::commit();

            return $vehiclePassage;
        } catch (Exception $exception) {

            DB::rollBack();

            Log::debug($exception);

            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     *
     */
    public function getVehicleInfosOrAlerts(string $immatriculationNumber)
    {
        $vehicle = getVehicleByImmatriculation($immatriculationNumber);
        $alertRepository = new VehicleAlertRepository();
        $alertTypes = null;
        if ($data = $alertRepository->getVehicleAlertsGroupByAlertType($vehicle)) {
            $alertTypes = AlertTypeResource::collection($data);
        }

        return [
            'is_alerted' => $alertRepository->isVehicleAlerted($vehicle),
            'alert_types' => $alertTypes,
            'vehicle' => new ClientVehicleResource($vehicle),
            'vehicle_owner' => new VehicleOwnerResource($vehicle->owner),
        ];
    }
}
