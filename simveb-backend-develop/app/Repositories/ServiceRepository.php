<?php

namespace App\Repositories;

use App\Http\Requests\ServiceRequest;
use App\Models\Action;
use App\Models\Config\Service;
use App\Models\Config\ServiceType;
use App\Models\Config\Organization;
use App\Models\Config\OwnerType;
use App\Models\Config\Step;
use App\Models\DemandAction;
use App\Models\ServiceStep;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Repositories\Demand\DemandRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ntech\RequiredDocumentPackage\Models\DocumentType;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ServiceRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function getActive(bool $paginate = true, $relations = []): mixed
    {

        $query = $this->model->newQuery()
            ->where('is_active', true)
            ->where('can_be_demanded', true)
            ->where('is_child', false)
            ->with(['children' => function ($query) {
                $query->where('is_active', true)
                    ->where('can_be_demanded', true)
                    ->orderByDesc('created_at');
            }])
            ->orderByDesc('created_at');

        return $query->get();
    }

    public function create()
    {
        $vehiclesCategories = VehicleCategory::query()->select(['id', 'label'])->get();
        $vehicleCharacteriticCategories = VehicleCharacteristicCategory::query()->select(['id', 'label'])->with('vehicleCharacteristics')->get();
        return [
            'types' => ServiceType::query()->select(['id', 'name',])->get(),
            'documents' => DocumentType::query()->get(),
            'organizations' => Organization::query()->get(),
            'services' => Service::query()->select(['id', 'name'])
                ->whereDoesntHave('children', function ($query) {
                })
                ->get(),
            'categories' => $vehiclesCategories,
            'characteristics' => $vehicleCharacteriticCategories,
            'steps' => Step::query()->select(['id', 'label'])->get(),
            'vehicle_categories' => [
                'categories' => $vehiclesCategories,
                'model_type' => VehicleCategory::class
            ],
            'vehicle_characteristic_categories' => [
                'vehicle_characteristics' => $vehicleCharacteriticCategories,
                'model_type' => VehicleCharacteristic::class
            ],
            'vehicle_owner_types' => [
                'owner_types' => OwnerType::query()->get(),
                'model_type' => OwnerType::class
            ],
        ];
    }

    public function store(array $data, $request = null): Model|null
    {
        DB::beginTransaction();
        try {
            if ($request->isNotFilled('children')) {
                $data += [
                    'is_child' => true
                ];
            }

            $service = parent::store($data, $request);
            if (filled($request->documents)) {
                $service->documents()->attach($request->documents);
            }
            if (filled($request->children)) {
                $service->children()->attach($request->children);
            }
            if (filled($request->vehicle_categories)) {
                $service->serviceVehicleCategories()->attach($request->vehicle_categories);
            }

            if (filled($request['steps'])) {
                foreach ($request['steps'] as $step) {
                    ServiceStep::updateOrCreate([
                        "service_id" => $service->id,
                        "step_id" => $step['step_id'],
                        "position" => $step['position'],
                    ], [
                        "duration" => $step['duration']
                    ]);
                }
            }


            if (filled($request['extra_services'])) {
                $service->serviceExtraServices()->attach($request['extra_services']);
            }

            if (filled($request['owner_price_variations'])) {
                $service->vehicleOwnerTypes()->attach($request['owner_price_variations']);
            }

            if (filled($request['category_price_variations'])) {
                $service->vehicleCategories()->attach($request['category_price_variations']);
            }

            if (filled($request['characteristic_price_variations'])) {
                $service->vehicleCharacteristics()->attach($request['characteristic_price_variations']);
            }

            DB::commit();
            return $service;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function show(Service $service)
    {
        $data = [];
        $characteristicsId = $service->vehicleCharacteristics()->pluck('id')->toArray();
        foreach (VehicleCharacteristicCategory::whereHas('vehicleCharacteristics', function ($q) use ($characteristicsId) {
            $q->whereIn('id', $characteristicsId);
        })->get() as $category) {
            $data[] = [
                'id' => $category->id,
                'name' => $category->name,
                'label' => $category->label,
                'characteristics' => $service->vehicleCharacteristics()->where('category_id', $category->id)->get(),
            ];
        }
        $service->vehicle_characteristics = $data;

        return $service->load($service::relations());
    }

    public function update(Model $model, array $data, $request = null): Model
    {
        DB::beginTransaction();
        $service =  parent::update($model, $data, $request);
        $service->documents()->sync($request->documents);

        if (filled($request['steps'])) {
            $stepsIds = $service->serviceSteps()->pluck('step_id')->toArray();
            $actualSteps = collect($request['steps'])->pluck('step_id')->toArray();
            $removedSteps = array_diff($stepsIds, $actualSteps);
            DemandAction::query()->whereIn('action_id', Action::query()
                ->whereHas('serviceStep',function($query) use($removedSteps, $service){
                    $query->whereIn('step_id', $removedSteps)
                    ->where('service_id', $service->id);
                })->pluck('id')->toArray())
                ->delete();
            Action::query()
                ->whereHas('serviceStep',function($query) use($removedSteps, $service){
                    $query->whereIn('step_id', $removedSteps)
                    ->where('service_id', $service->id);
                })
                ->delete();
            ServiceStep::query()->where('service_id', $service->id)
                ->whereIn('step_id', $removedSteps)
                ->delete();

            foreach ($request['steps'] as $step) {
                ServiceStep::updateOrCreate([
                    "service_id" => $service->id,
                    "step_id" => $step['step_id'],
                ], [
                    "position" => $step['position'],
                    "duration" => $step['duration']
                ]);
            }
        }

        if ($request->filled('children')) {
            $service->children()->sync($request->children);
        }

        if (filled($request['extra_services'])) {
            $service->serviceExtraServices()->sync($request['extra_services']);
        }

        if (filled($request['owner_price_variations'])) {
            $service->vehicleOwnerTypes()->sync($request['owner_price_variations']);
        }

        if (filled($request['category_price_variations'])) {
            $service->vehicleCategories()->sync($request['category_price_variations']);
        }

        if (filled($request['characteristic_price_variations'])) {
            $service->vehicleCharacteristics()->sync($request['characteristic_price_variations']);
        }

        DB::commit();
        return $service->refresh();
    }

    public function toggleService(Service $model, $request = null): Model
    {
        $model->update([
            'is_active' => $request->is_active,
            'deactived_by' => getOnlineProfile()?->id,
            'deactived_at' => now()
        ]);
        return $model;
    }

    /**
     *
     */
    public function getMostPopulars()
    {
        $max = (new DemandRepository)->getTotalDemandByService()->max('demands_count');
        return $this->model->newQuery()->select(['name', 'code'])->withCount('demands')->has('demands', '>=', $max)->filter()->get();
    }

    /**
     *
     */
    public function getUnpopulars()
    {
        $min = (new DemandRepository)->getTotalDemandByService()->min('demands_count');
        return $this->model->newQuery()->select(['name', 'code'])->withCount('demands')->has('demands', '<=', $min)->filter()->get();
    }
}
