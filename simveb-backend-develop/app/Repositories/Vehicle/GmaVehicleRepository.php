<?php

namespace App\Repositories\Vehicle;

use App\Enums\InstitutionTypesEnum;
use App\Enums\Status;
use App\Imports\GmaVehicleImport;
use App\Models\Institution\Institution;
use App\Models\Institution\InstitutionType;
use App\Models\SimvebFile;
use App\Models\Vehicle\GmaVehicle;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Services\HandleExcelErrorsService;
use App\Services\VehicleService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Http\Request;

class GmaVehicleRepository extends AbstractCrudRepository
{
    use UploadFile;
    private HandleExcelErrorsService $service;
    private $fileErrorPath = '';
    private VehicleService $vehicleService;

    public function __construct()
    {
        parent::__construct(GmaVehicle::class);
        $this->service = new HandleExcelErrorsService;
        $this->vehicleService = new VehicleService;
    }

    /**
     *
     */
    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        return $this->model->newQuery()
            ->with([
                'author',
                'author.identity',
                'institution',
            ])
            ->orderByDesc('created_at')
            ->filter()
            ->paginate();
    }

    /**
     * @return array
     */
    public function create(): Collection
    {
        return Institution::query()->where(
            'type_id',
            InstitutionType::query()->where('name', InstitutionTypesEnum::io->name)->first()?->id
        )->orWhere(
            'type_id',
            InstitutionType::query()->where('name', InstitutionTypesEnum::ngo->name)->first()?->id
        )->select('id', 'name')->get();
    }


    public function store(array $data, $request = null): Model|null
    {
        DB::beginTransaction();
        try {
            $vehicle = $this->vehicleService->getVehicleByVinOrImmatriculation(["vin" => $data['vin']]);
            $data['vehicle_id'] = $vehicle->id;

            $declarationFile = Arr::pull($data, 'declaration_file');

            $gmaVehicle = parent::store($data);

            if ($declarationFile) {
                $fileInfo = $this->saveFile($declarationFile, "declaration_files");

                $gmaVehicle->file()->create([
                    'path' => $fileInfo,
                    'type' => SimvebFile::FILE,
                ]);
            }

            DB::commit();

            return $gmaVehicle->load($gmaVehicle::relations())->refresh();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function update(Model $model, array $data, $request = null): Model
    {
        DB::beginTransaction();
        try {
            $declarationFile = Arr::pull($data, 'declaration_file');

            $gmaVehicle = parent::update($model, $data, $request);

            if ($declarationFile) {
                $gmaVehicle->file()->delete();

                $fileInfo = $this->saveFile($declarationFile, 'declaration_files');

                $gmaVehicle->file()->create([
                    'path' => $fileInfo,
                    'type' => SimvebFile::FILE,
                ]);
            }

            DB::commit();

            return $gmaVehicle->load($gmaVehicle::relations())->refresh();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function importGmaVehicle(array $data): array
    {
        try {
            $declarationFile = Arr::pull($data, 'declaration_file');
            $file = Arr::pull($data, 'file');

            $import = new GmaVehicleImport($declarationFile, getOnlineProfile());

            $import->import($file);

            if (isset($import->failures()[0])) {
                $this->fileErrorPath = $this->service->HandleExcelErrors($import, $file, 'gma_vehicle');

                return ['message' => 'Importation effectuée avec quelques erreurs, vous pouvez télécharger le fichier d\'erreur ou le retrouver dans votre boite mail.', 'ErrorFile' => $this->fileErrorPath];
            }

            return ['message' => 'Importation réussie.'];
        } catch (\Exception $exception) {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('L\'importation a échoué'));
        }
    }

    public function validate($request): array
    {
        $gmaVehicles = [];
        foreach ($request['gma_vehicles'] as $value) {
            $gmaVehicle = GmaVehicle::find($value);
            $gmaVehicle->update([
                'status' => Status::validated->name,
                'validated_by' => getOnlineProfile()?->id,
                'validated_at' => now()
            ]);
            $gmaVehicles[] = $gmaVehicle;
        }

        return $gmaVehicles;
    }

    public function reject($request): array
    {
        $gmaVehicles = [];
        foreach ($request['gma_vehicles'] as $value) {
            $gmaVehicle = GmaVehicle::find($value['id']);
            $gmaVehicle->update([
                'status' => Status::rejected->name,
                'rejected_reason' => $value['reason'],
                'rejected_by' => getOnlineProfile()?->id,
                'rejected_at' => now()
            ]);
            $gmaVehicles[] = $gmaVehicle;
        }

        return $gmaVehicles;
    }

    public function stats($request): array
    {
        $recordsByMonth = [];

        for ($month = 1; $month <= 12; $month++) {
            $recordsByMonth[$month] = 0;
        }

        $registration = $this->model->newQuery()
            ->whereYear('updated_at', Carbon::now()->year)
            ->selectRaw('EXTRACT(MONTH FROM updated_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');


        foreach ($registration as $month => $count) {
            $recordsByMonth[$month] = $count;
        }

        $registrationData = [];
        foreach ($recordsByMonth as $month => $count) {
            $monthName = Carbon::create()->month($month)->format('F');
            $registrationData["$monthName"] = $count;
        }

        $statisticStartDate = Carbon::parse(Carbon::now());
        $statisticEndDate = Carbon::parse(Carbon::now());

        $generalViewStartDate = Carbon::parse(Carbon::now());
        $generalViewEndDate = Carbon::parse(Carbon::now());

        if ($request['statistics_filter'][0]['type'] == 'manual') {
            $statisticStartDate = Carbon::parse($request['statistics_filter'][0]['start_date']);
            $statisticEndDate = Carbon::parse($request['statistics_filter'][0]['end_date']);
        } elseif ($request['statistics_filter'][0]['type'] == 'year') {
            $statisticStartDate = Carbon::parse(Carbon::now())->startOfYear();
            $statisticEndDate = Carbon::parse(Carbon::now())->startOfYear();
        } elseif ($request['statistics_filter'][0]['type'] == 'month') {
            $statisticStartDate = Carbon::parse(Carbon::now())->startOfMonth();
            $statisticEndDate = Carbon::parse(Carbon::now())->startOfMonth();
        } else {
            $statisticStartDate = Carbon::parse(Carbon::now())->startOfWeek();
            $statisticEndDate = Carbon::parse(Carbon::now())->endOfWeek();
        }

        if ($request['statistics_filter'][0]['type'] == 'manual') {
            $generalViewStartDate = Carbon::parse($request['statistics_filter'][0]['start_date']);
            $generalViewEndDate = Carbon::parse($request['statistics_filter'][0]['end_date']);
        } elseif ($request['statistics_filter'][0]['type'] == 'year') {
            $generalViewStartDate = Carbon::parse(Carbon::now())->startOfYear();
            $generalViewEndDate = Carbon::parse(Carbon::now())->startOfYear();
        } elseif ($request['statistics_filter'][0]['type'] == 'month') {
            $generalViewStartDate = Carbon::parse(Carbon::now())->startOfMonth();
            $generalViewEndDate = Carbon::parse(Carbon::now())->startOfMonth();
        } else {
            $generalViewStartDate = Carbon::parse(Carbon::now())->startOfWeek();
            $generalViewEndDate = Carbon::parse(Carbon::now())->endOfWeek();
        }

        return [
            'statistics' => [
                'recorded' => $this->model->newQuery()
                    ->where('updated_at', '>', $statisticStartDate)
                    ->where('updated_at', '<', $statisticEndDate)
                    ->where('status', Status::recorded->name)->count(),
                'changes' => $this->model->newQuery()
                    ->where('updated_at', '>', $statisticStartDate)
                    ->where('updated_at', '<', $statisticEndDate)
                    ->where('status', Status::validated->name)
                    ->orWhere('status', Status::plate_removed->name)->count(),
                'removed' => $this->model->newQuery()
                    ->where('updated_at', '>', $statisticStartDate)
                    ->where('updated_at', '<', $statisticEndDate)
                    ->where('status', Status::plate_removed->name)->count(),
            ],
            'registration' => $registrationData,
            'general_view' => [
                'validated' => $this->model->newQuery()
                    ->where('updated_at', '>', $generalViewStartDate)
                    ->where('updated_at', '<', $generalViewEndDate)
                    ->where('status', Status::validated->name)->count(),
                'pending' => $this->model->newQuery()
                    ->where('updated_at', '>', $generalViewStartDate)
                    ->where('updated_at', '<', $generalViewEndDate)
                    ->where('status', Status::recorded->name)->count(),
                'rejected' => $this->model->newQuery()
                    ->where('updated_at', '>', $generalViewStartDate)
                    ->where('updated_at', '<', $generalViewEndDate)
                    ->where('status', Status::rejected->name)->count(),
            ]
        ];
    }
}
