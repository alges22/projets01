<?php

namespace App\Repositories\Demand;

use App\Enums\Status;
use App\Exports\DemandsExport;
use App\Http\Resources\ClientDemandCollection;
use App\Models\Config\Service;
use App\Models\Order\Demand;
use App\Models\Vehicle\VehicleCategory;
use App\Repositories\Crud\CrudRepositoryHandler;
use App\Services\GeneratePdfService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DemandRepository extends CrudRepositoryHandler
{
    public function __construct()
    {
        parent::__construct(Demand::class);
    }

    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        $query = $this->model->newQuery()
            ->select([
                'demands.id',
                'demands.reference',
                'demands.created_at',
                'demands.vehicle_owner_id',
                'demands.vehicle_id',
                'demands.status',
                'demands.model_id',
                'demands.model_type',
                'demands.service_id',
                'demands.active_treatment_id',
                'demands.submitted_at',
                'services.duration'
            ])
            ->join('services', 'services.id', '=', 'demands.service_id')
            ->with([
                'vehicleOwner:id,identity_id' => ['identity:id,lastname,firstname,email,telephone'],
                'service:id,name,code,type_id',
                'vehicle',
                'model'
            ])
            ->whereNotIn('demands.status', [Status::pending->name, Status::in_cart->name])
            ->when(request()->input('is_late'), function ($query) {
                $query->whereRaw('EXTRACT(DAY FROM (CURRENT_DATE - demands.submitted_at)) > services.duration')
                    ->whereNotIn('demands.status', [Status::closed->name]);
            })
            ->when(request()->input('days_late'), function ($query) {
                $query->whereRaw('EXTRACT(DAY FROM (CURRENT_DATE - demands.submitted_at)) - services.duration = ?', [request()->input('days_late')])
                    ->whereNotIn('demands.status', [Status::closed->name]);
            })
            ->filter()
            ->orderByDesc('demands.created_at');

        return $query;
    }

    public function getInterpolDemands(bool $paginate = true, $relations = []): mixed
    {
        $query = $this->model->newQuery()
            ->select(['id', 'reference', 'created_at', 'vehicle_owner_id', 'vehicle_id', 'status', 'model_id', 'model_type', 'service_id', 'active_treatment_id'])
            ->with([
                'vehicleOwner:id,identity_id' => ['identity:id,lastname,firstname,email,telephone'],
                'service:id,name,code,type_id',
                'vehicle',
            ])
            ->selectRaw("*, (SELECT COUNT(*) FROM blacklist_vehicles WHERE vehicle_id = demands.vehicle_id and status = 'validated') as is_blacklisted")
            ->where('status', Status::affected_to_interpol->name)
            ->orderByDesc('created_at')
            ->filter()
            ->paginate(request()->input('per_page', 15));

        return $query;
    }


    public function findDemandByService(string $serviceId, string $vehicleId)
    {
        return $this->model->newQuery()->where([
            'vehicle_id' => $vehicleId,
            'service_id' => $serviceId,
        ])->first();
    }

    public function getClientDemands()
    {
        if (getOnlineProfile()->isUserProfile()) {
            $query = [
                'profile_id' => getOnlineProfile()->id
            ];
        } else {
            $query = [
                'institution_id' => getOnlineProfile()->institution_id
            ];
        }

        $demands =  $this->model->newQuery()
            ->filter()
            ->orderBy('demands.created_at', 'desc')
            ->select([
                'id',
                'model_id',
                'model_type',
                'vehicle_owner_id',
                'vehicle_id',
                'service_id',
                'reference',
                'created_at',
                'status',
                'active_treatment_id'
            ])
            ->with(['service:id,name,code,type_id', 'activeTreatment:id'])
            ->where($query)
            ->whereNotIn('status', [Status::pending->name, Status::in_cart]);

        if ((bool) request('_no_paginate')) {
            return new ClientDemandCollection($demands->get());
        }
        return new ClientDemandCollection($demands->paginate(request()->input('per_page', 15)));
    }

    public function getClientPendingDemands()
    {
        if (getOnlineProfile()->isUserProfile()) {
            $query = [
                'profile_id' => getOnlineProfile()->id
            ];
        } else {
            $query = [
                'institution_id' => getOnlineProfile()->institution_id
            ];
        }

        return $this->model->newQuery()
            ->select(['id', 'vehicle_owner_id', 'vehicle_id', 'service_id', 'reference', 'created_at', 'model_id', 'model_type', 'status'])
            ->with(['service:id,name,code,type_id'])
            ->where($query)
            ->where('status', Status::pending->name)
            ->filter()
            ->get();
    }

    public function storeUpUpdate(array $data): Model
    {
        return $this->model->newQuery()->updateOrCreate([
            'service_id' => $data['service_id'],
            'vehicle_id' => $data['vehicle_id'],
        ], $data);
    }

    public function myPendingDemands()
    {
        $query = $this->model->newQuery()
            ->whereHas('demandActions', function ($q) {
                $q->where('profile_id', getOnlineProfile()->id)
                    ->where('status', Status::pending->name);
            })
            ->select(['id', 'reference', 'created_at', 'vehicle_owner_id', 'vehicle_id', 'status', 'model_id', 'model_type', 'service_id', 'active_treatment_id'])
            ->with([
                'vehicleOwner:id,identity_id' => ['identity:id,lastname,firstname,email,telephone'],
                'service:id,name,code,type_id',
                'vehicle',
            ])
            ->orderBy('created_at', 'desc')->filter();

        $paginate = request('paginate') && in_array(request('paginate'), ['true', '1', 1]);

        return $paginate ? $query->paginate(request()->input('per_page', 15)) : $query->get();
    }

    public function myTreatedDemands()
    {
        $query = $this->model->newQuery()
            ->whereHas('demandActions', function ($q) {
                $q->where('profile_id', getOnlineProfile()->id)
                    ->where('status', Status::done->name);
            })
            ->select(['id', 'reference', 'created_at', 'vehicle_owner_id', 'vehicle_id', 'status', 'model_id', 'model_type', 'service_id', 'active_treatment_id'])
            ->with([
                'vehicleOwner:id,identity_id' => ['identity:id,lastname,firstname,email,telephone'],
                'service:id,name,code,type_id',
                'vehicle',
            ])
            ->filter()
            ->orderBy('created_at', 'desc');

        $paginate = request('paginate') && in_array(request('paginate'), ['true', '1', 1]);

        return $paginate ? $query->paginate(request()->input('per_page', 15)) : $query->get();
    }

    public function getTreatedDemandsByProfile($profileId)
    {
        $query = $this->model->newQuery()
            ->whereHas('treatments', function ($q) use ($profileId) {
                $q->where('responsible_id', $profileId)
                    ->where('status', Status::done->name);
            })
            ->select(['id', 'reference', 'created_at', 'vehicle_owner_id', 'vehicle_id', 'status', 'model_id', 'model_type', 'service_id', 'active_treatment_id'])
            ->with([
                'vehicleOwner:id,identity_id' => ['identity:id,lastname,firstname,email,telephone'],
                'service:id,name,code,type_id',
                'vehicle',
            ])
            ->orderBy('created_at', 'desc')->filter();

        $paginate = request('paginate') && in_array(request('paginate'), ['true', '1', 1]);

        return $paginate ? $query->paginate(request()->input('per_page', 15)) : $query->get();
    }
    /**
     *
     */
    public function getAllDemandsTotal(bool $paginate = true, $relations = []): mixed
    {
        $query = $this->model->newQuery()
            ->join('services', 'services.id', '=', 'demands.service_id')
            ->whereNotIn('demands.status', [Status::pending->name, Status::in_cart->name]);

        if (request()->input('is_late')) {
            $query->whereRaw('EXTRACT(DAY FROM (CURRENT_DATE - demands.submitted_at)) > services.duration')
                ->whereNotIn('demands.status', [Status::closed->name]);
        }

        if ($daysLate = request()->input('days_late')) {
            $query->whereRaw('EXTRACT(DAY FROM (CURRENT_DATE - demands.submitted_at)) - services.duration = ?', [$daysLate])
                ->whereNotIn('demands.status', [Status::closed->name]);
        }

        $demandsTotal = $query->count();

        $mostDemandedService = Service::query()
            ->join('demands', 'demands.service_id', '=', 'services.id')
            ->select('services.id', 'services.name')
            ->selectRaw('COUNT(demands.id) as demand_count')
            ->whereNotIn('demands.status', [Status::pending->name, Status::in_cart->name])
            ->when(request()->input('is_late'), function ($query) {
                $query->whereRaw('EXTRACT(DAY FROM (CURRENT_DATE - demands.submitted_at)) > services.duration')
                    ->whereNotIn('demands.status', [Status::closed->name]);
            })
            ->when(request()->input('days_late'), function ($query) {
                $query->whereRaw('EXTRACT(DAY FROM (CURRENT_DATE - demands.submitted_at)) - services.duration = ?', [request()->input('days_late')])
                    ->whereNotIn('demands.status', [Status::closed->name]);
            })
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('demand_count') // Service le plus demandé
            ->first();

        $leastDemandedService = Service::query()
            ->join('demands', 'demands.service_id', '=', 'services.id')
            ->select('services.id', 'services.name')
            ->selectRaw('COUNT(demands.id) as demand_count')
            ->whereNotIn('demands.status', [Status::pending->name, Status::in_cart->name])
            ->when(request()->input('is_late'), function ($query) {
                $query->whereRaw('EXTRACT(DAY FROM (CURRENT_DATE - demands.submitted_at)) > services.duration')
                    ->whereNotIn('demands.status', [Status::closed->name]);
            })
            ->when(request()->input('days_late'), function ($query) {
                $query->whereRaw('EXTRACT(DAY FROM (CURRENT_DATE - demands.submitted_at)) - services.duration = ?', [request()->input('days_late')])
                    ->whereNotIn('demands.status', [Status::closed->name]);
            })
            ->groupBy('services.id', 'services.name')
            ->orderBy('demand_count')
            ->first();

        return [
            'demands_total' => $demandsTotal,
            'most_demanded_service' => $mostDemandedService,
            'least_demanded_service' => $leastDemandedService,
        ];
    }

    /**
     *
     */
    public function getPendingDemandsTotal()
    {
        return [
            'pending_demands_total' => Demand::query()->where('status', Status::pending->name)->filter()->count()
        ];
    }

    /**
     *
     */
    public function getInCartDemandsTotal()
    {
        return [
            'in_cart_demands_total' => Demand::query()->where('status', Status::pending->name)->filter()->count()
        ];
    }

    /**
     *
     */
    public function getCanceledDemandsTotal()
    {
        return [
            'canceled_demands_total' => Demand::query()->where('status', Status::canceled->name)->filter()->count()
        ];
    }

    /**
     *
     */
    public function getTotalDemandByService()
    {
        foreach (Service::select(['id', 'code', 'name'])->get() as $service) {
            $count = Demand::query()->where('service_id', $service->id)->filter()->count();
            $data[] = [
                'name' => $service->name,
                'code' => $service->code,
                'demands' => $count
            ];
        }

        return $data;
    }

    /**
     *
     */
    public function getVehicleCategoryDemandsTotal()
    {
        foreach (VehicleCategory::select(['id', 'name', 'label'])->get() as $category) {
            $count = Demand::withWhereHas('vehicle', function ($query) use ($category) {
                $query->where('vehicle_category_id', $category->id);
            })
                ->filter()
                ->count();
            $data[] = [
                'name' => $category->name,
                'label' => $category->label,
                'demands' => $count,
            ];
        }
    }

    /**
     *
     */
    public function getOverdueDemands()
    {
        $demands = Demand::query()->with(['service'])->get();
        $count = $demands->filter(function ($value, $key) {
            return $value->service->duration < Carbon::parse($value->created_at)->floatDiffInDays();
        })->count();

        return [
            'overdue_demands_total' => $count
        ];
    }

    public function changeUpdatesStatus($demandId, $status)
    {
        $demand = Demand::find($demandId);
        $demand->updates_status = $status;
        return $demand->save();
    }

    public function validateUpdates($demandId)
    {
        $demandUpdatesHistoryRepository = new DemandUpdatesHistoryRepository();
        $history_is_validated = $demandUpdatesHistoryRepository->isHistoryValidated($demandId);
        if ($history_is_validated) {
            $demand = Demand::find($demandId);
            $demand->updates_status = Status::not_available->name;
            return $demand->save();
        } else {
            return null;
        }
    }

    /**
     *
     */
    public function excelExport()
    {
        $query = $this->getAll(false);
        return (new DemandsExport($query))->download('demands.xlsx');
    }

    /**
     *
     */
    public function pdfExport()
    {
        $query = $this->getAll(false);
        return GeneratePdfService::generatePDF(
            view: 'exports.pdf.demands',
            data: ['demands' => $query->get()],
            filename: 'demands_' . now()->timestamp . '_' . Str::random(3) . '.pdf',
            folder: 'demands/' . Str::snake(class_basename($this)) . '_demands',
            stream: true,
        );
    }
}
