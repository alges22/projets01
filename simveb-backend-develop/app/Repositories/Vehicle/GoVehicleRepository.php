<?php

namespace App\Repositories\Vehicle;


use App\Enums\ProfileTypesEnum;
use App\Imports\GovVehicleImport;
use App\Models\Auth\Profile;
use App\Models\Vehicle\GovVehicle;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Services\HandleExcelErrorsService;
use App\Services\InvitationService;
use App\Services\VehicleService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GoVehicleRepository extends AbstractCrudRepository
{
    private InvitationService $invitationService;
    private HandleExcelErrorsService $service;
	private $fileErrorPath = '';
    private VehicleService $vehicleService;

    public function __construct()
    {
        parent::__construct(GovVehicle::class);
        $this->invitationService = new InvitationService;
        $this->service = new HandleExcelErrorsService;
        $this->vehicleService = new VehicleService;
    }


    public function store(array $data, $request = null): Model|null
    {
        DB::beginTransaction();
        try {
            if (!$profile = Profile::query()
                ->whereRelation('type','code', ProfileTypesEnum::central_garage->name)
                ->first()){
                $this->invitationService->store(['npi' => $data['owner_npi']]);

                $data['profile_id'] = $profile->id;
            }

            $vehicle = $this->vehicleService->getVehicleByVinOrImmatriculation(["vin" => $data['vin']]);
            $data['vehicle_id'] = $vehicle->id;

            $govVehicle = parent::store($data);
            DB::commit();
            $govVehicle->load($govVehicle::relations())->refresh();
            return $govVehicle;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function update(Model $vehicle, array $data, $request = null): Model
    {
        DB::beginTransaction();
        try {
            if ($vehicle->profile_id == null && !$profile = Profile::query()

                ->whereRelation('type','code', ProfileTypesEnum::central_garage->name)
                ->first()){
                $this->invitationService->store(['npi' => $data['owner_npi']]);

                $data['profile_id'] = $profile->id;
            }
            $vehicle = parent::update($vehicle, $data, $request);

            DB::commit();
            return $vehicle;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
    public function importGovVehicle(Request $request): array
    {
        try
        {
            $file = $request->file('file');

            $import = new GovVehicleImport;
            $import->import($file);

            if (isset($import->failures()[0])) {
                $this->fileErrorPath = $this->service->HandleExcelErrors($import, $file, 'gov_vehicle');

                return ['message' => 'Importation effectuée avec quelques erreurs, vous pouvez télécharger le fichier d\'erreur ou le retrouver dans votre boite mail.', 'ErrorFile' => $this->fileErrorPath];
            }

            return ['message' => "Importation réussie."];
        }catch (\Exception $exception)
        {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('L\'importation a échoué'));
        }
    }

}
