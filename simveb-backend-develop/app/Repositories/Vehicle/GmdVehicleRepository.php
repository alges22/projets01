<?php

namespace App\Repositories\Vehicle;

use App\Enums\InstitutionTypesEnum;
use App\Enums\Status;
use App\Imports\GmdVehicleImport;
use App\Models\Institution\Institution;
use App\Models\Institution\InstitutionType;
use App\Models\SimvebFile;
use App\Models\Vehicle\GmdVehicle;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Services\HandleExcelErrorsService;
use App\Services\VehicleService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class GmdVehicleRepository extends AbstractCrudRepository
{
    private $vehicleService;
    private HandleExcelErrorsService $service;
    private $fileErrorPath = '';

    public function __construct()
    {
        parent::__construct(GmdVehicle::class);
        $this->vehicleService = new VehicleService;
    }

    /**
     * @param bool $paginate
     * return list of model
     * @return mixed
     */
    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        $query = $this->model
            ->newQuery()
            ->with([
                'author',
                'author.identity',
                'institution',
            ])
            ->orderByDesc('created_at');

        return $paginate
            ? $query->filter()->paginate(request('per_page', '15'))
            : $query->filter()->get();
    }

    /**
     * @return array
     */
    public function create(): array
    {
        $institutionTypeIds = InstitutionType::query()
            ->where('name', InstitutionTypesEnum::embassie->name)
            ->orWhere('name', InstitutionTypesEnum::consulate->name)
            ->pluck('id')
            ->toArray();
        return [
            'import_model' => file_exists(public_path('format-import/gmd_vehicle_template.xlsx')) ? asset('format-import/gmd_vehicle_template.xlsx') : "",
            'institutions' => Institution::query()->whereIn('type_id', $institutionTypeIds)->select(['id', 'name'])->get(),
        ];
    }

    /**
     * @param array $data
     * @param $request
     * @return Model|null
     */
    public function store(array $data, $request = null): ?Model
    {
        DB::beginTransaction();
        try {
            $vehicle = $this->vehicleService->getVehicleByVinOrImmatriculation(["vin" => $data['vin']]);
            $data['vehicle_id'] = $vehicle->id;
            $data['authored_at'] = now();

            $declarationFile = Arr::pull($data, 'declaration_file');

            $gmdVehicle = parent::store($data);

            if ($declarationFile) {
                $fileInfo = $this->saveFile($declarationFile, 'declaration_files');

                $gmdVehicle->file()->create([
                    'path' => $fileInfo,
                    'type' => SimvebFile::FILE,
                ]);
            }

            DB::commit();

            return $gmdVehicle->load($gmdVehicle::relations())->refresh();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * @param Model $model
     * @param array $data
     * @param $request
     * @return Model
     */
    function update(Model $model, array $data, $request = null): Model
    {
        DB::beginTransaction();
        try {
            $vehicle = $this->vehicleService->getVehicleByVinOrImmatriculation(["vin" => $data['vin']]);
            $data['vehicle_id'] = $vehicle->id;

            $declarationFile = Arr::pull($data, 'declaration_file');

            $gmdVehicle = parent::update($model, $data);

            if ($declarationFile) {
                $gmdVehicle->file()->delete();

                $fileInfo = $this->saveFile($declarationFile, 'declaration_files');

                $gmdVehicle->file()->create([
                    'path' => $fileInfo,
                    'type' => SimvebFile::FILE,
                ]);
            }

            DB::commit();

            return $gmdVehicle->load($gmdVehicle::relations())->refresh();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * @return mixed
     */
    public function validate(array $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['gmd_vehicles'] as $vehicle_id) {
                $vehicleData['validated_by'] = $data['validator_id'];
                $vehicleData['validated_at'] = now();
                $vehicleData['status'] = Status::validated->name;

                $gmdVehicles[] = parent::update(GmdVehicle::find($vehicle_id), $vehicleData);
            }
            DB::commit();

            return $gmdVehicles;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function reject($request): array
    {
        $gmdVehicles = [];
        foreach ($request['gmd_vehicles'] as $value) {
            $gmdVehicle = GmdVehicle::find($value['id']);
            $gmdVehicle->update([
                'status' => Status::rejected->name,
                'rejected_reason' => $value['reason'],
                'rejected_by' => getOnlineProfile()?->id,
                'rejected_at' => now()
            ]);
            $gmdVehicles[] = $gmdVehicle;
        }

        return $gmdVehicles;
    }

    /**
     * @return array
     */
    public function import(array $data): array
    {
        try {
            $declarationFile = Arr::pull($data, 'declaration_file');
            $file = Arr::pull($data, 'file');

            $import = new GmdVehicleImport($declarationFile, getOnlineProfile());

            $import->import($file);

            if (isset($import->failures()[0])) {
                $this->fileErrorPath = $this->service->HandleExcelErrors($import, $file, 'gmd_vehicle');

                return ['message' => 'Importation effectuée avec quelques erreurs, vous pouvez télécharger le fichier d\'erreur ou le retrouver dans votre boite mail.', 'ErrorFile' => $this->fileErrorPath];
            }

            return ['message' => 'Importation réussie.'];
        } catch (\Exception $exception) {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('L\'importation a échoué'));
        }
    }
}
