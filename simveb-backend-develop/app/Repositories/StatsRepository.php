<?php

namespace App\Repositories;

use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Enums\VehiclePassageType;
use App\Models\Auction\AuctionSaleDeclaration;
use App\Models\Auction\AuctionSaleVehicle;
use App\Models\Config\BlacklistVehicle;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Opposition;
use App\Models\Order\Demand;
use App\Models\Plate;
use App\Models\Pledge;
use App\Models\Reform\ReformDeclaration;
use App\Models\Treatment\PrintOrder;
use App\Models\Vehicle\GmaVehicle;
use App\Models\Vehicle\GmdVehicle;
use App\Models\Vehicle\GovVehicle;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleAlert;
use App\Models\Vehicle\VehiclePassage;
use App\Repositories\Plate\PlateRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class StatsRepository
{

    public function getAllStats(): array | null
    {
        $stats = [];
        $onlineProfile = getOnlineProfile();
        switch ($onlineProfile->type->name) {
            case ProfileTypesEnum::anatt->value:
                $stats = $this->anattStats();
                break;

            case ProfileTypesEnum::auctioneer->value:
                $stats = [
                    'total_auction_sale_declarations' => AuctionSaleDeclaration::where('auctioneer_id', $onlineProfile->id)->count(),
                    'total_auction_sale_vehicles' => AuctionSaleVehicle::whereRelation('declaration', 'auctioneer_id', $onlineProfile->id)->count(),
                    'total_reform_declarations' => ReformDeclaration::whereRelation('auctionSaleDeclaration', 'auctioneer_id', $onlineProfile->id)->count(),
                    'latest_activities' => Activity::where('causer_id', $onlineProfile->id)->with(['causer:id,identity_id', 'causer.identity:id,firstname,lastname'])
                        ->latest()->limit(20)->get(),
                ];
                break;

            case ProfileTypesEnum::company->value:
                $stats = null;
                break;

            case ProfileTypesEnum::distributor->value:
                $stats = [
                    'all' => $onlineProfile->institution->demands->count(),
                    'validated' => $onlineProfile->institution->demands()->where('status', Status::closed->name)->count(),
                    'rejected' => $onlineProfile->institution->demands()->where('status', Status::rejected->name)->count(),
                    'pending' => $onlineProfile->institution->demands()->whereNotIn('status', [Status::closed->name, Status::rejected->name, Status::in_cart->name])->count()
                ];
                break;

            case ProfileTypesEnum::bank->value:
                $stats = [
                    'pledges' => [
                        'all' => Pledge::where('institution_emitted_id', $onlineProfile->institution_id)->orWhere('financial_institution', $onlineProfile->institution_id)
                            ->count(),
                        'validated' => Pledge::where('status',)->where('institution_emitted_id', $onlineProfile->institution_id)->orWhere('financial_institution', $onlineProfile->institution_id)
                            ->count(),
                        'canceled' => Pledge::whereIn('status', [Status::institution_rejected->name, Status::justice_rejected->name, Status::anatt_rejected->name])
                            ->where('institution_emitted_id', $onlineProfile->institution_id)->orWhere('financial_institution', $onlineProfile->institution_id)
                            ->count(),
                    ]
                ];
                break;

            case ProfileTypesEnum::approved->value:
                $stats = [
                    'used_plates' => Plate::where('institution_id', $onlineProfile->institution_id)->whereNotNull('rfid')->count(),
                    'free_plates' => Plate::where('institution_id', $onlineProfile->institution_id)->whereNull('rfid')->count(),
                    'print_order_proccessed' => PrintOrder::where('status', Status::validated->name)->count()
                ];
                break;

            case ProfileTypesEnum::affiliate->value:
                $stats = [
                    'all' => $onlineProfile->institution->demands->count(),
                    'validated' => $onlineProfile->institution->demands()->where('status', Status::closed->name)->count(),
                    'rejected' => $onlineProfile->institution->demands()->where('status', Status::rejected->name)->count(),
                    'pending' => $onlineProfile->institution->demands()->whereNotIn('status', [Status::closed->name, Status::rejected->name, Status::in_cart->name])->count()
                ];
                break;

            case ProfileTypesEnum::interpol->value:
                $stats = [
                    'blacklisted_vehicles' => BlacklistVehicle::where('status', Status::validated->name)->count(),
                    'pending_vehicles' => BlacklistVehicle::where('status', Status::submitted->name)->count()
                ];
                break;

            case ProfileTypesEnum::police->value:
                $stats = [
                    'passages' => VehiclePassage::where('officer_id', $onlineProfile->id)->count(),
                    'inputs' => VehiclePassage::where('officer_id', $onlineProfile->id)->where('passage_type', VehiclePassageType::in->name)->count(),
                    'outputs' => VehiclePassage::where('officer_id', $onlineProfile->id)->where('passage_type', VehiclePassageType::out->name)->count(),
                    'alerts' => VehicleAlert::where('author_id', $onlineProfile->id)->count()
                ];
                break;

            case ProfileTypesEnum::central_garage->value:
                $stats = [
                    'recorded_vehicles' => GovVehicle::count(),
                    'changes' => '',
                    'removal' => ''
                ];
                break;

            case ProfileTypesEnum::gma->value:
                $stats = [
                    'recorded_vehicles' => [
                        'pending' => GmaVehicle::where('status', Status::recorded->name)->count(),
                        'validated' => GmaVehicle::where('status', Status::validated->name)->count(),
                        'rejected' => GmaVehicle::where('status', Status::rejected->name)->count()
                    ],
                    'changes' => '',
                    'removal' => ''
                ];
                break;

            case ProfileTypesEnum::gmd->value:
                $stats = [
                    'recorded_vehicles' => [
                        'pending' => GmdVehicle::where('status', Status::submitted->name)->count(),
                        'validated' => GmdVehicle::where('status', Status::validated->name)->count(),
                        'rejected' => GmdVehicle::where('status', Status::rejected->name)->count()
                    ],
                    'changes' => '',
                    'removal' => ''
                ];
                break;

            case ProfileTypesEnum::court->value:
                $stats = [
                    'pledges' => [
                        'all' => Pledge::whereHas('activeTreatment', function ($query) use ($onlineProfile) {
                            $query->where('treated_by', $onlineProfile->id);
                        })->orWhere('author_id', $onlineProfile->id)
                            ->count(),
                        'validated' => Pledge::where('status', Status::anatt_validated->name)->whereHas('activeTreatment', function ($query) use ($onlineProfile) {
                            $query->where('treated_by', $onlineProfile->id);
                        })->orWhere('author_id', $onlineProfile->id)
                            ->count(),
                        'canceled' => Pledge::whereIn('status', [Status::institution_rejected->name, Status::anatt_rejected->name, Status::justice_rejected->name])->whereHas('activeTreatment', function ($query) use ($onlineProfile) {
                            $query->where('treated_by', $onlineProfile->id);
                        })->orWhere('author_id', $onlineProfile->id)
                            ->count(),
                    ],
                    'oppositions' => Opposition::whereRelation('activeTreatment', 'treated_by', '=', $onlineProfile->id)->count(),
                ];
                break;

            default:
                $stats = null;
                break;
        }
        return $stats;
    }

    public function immatriculationDemand()
    {
        return [
            'two_wheels' => Demand::whereHas('service', function ($q) {
                $q->where('code', 'AS-IM2');
            })->count(),
            'three_wheels' => Demand::whereHas('service', function ($q) {
                $q->where('code', 'AS-IM3');
            })->count(),
            'four_wheels' => Demand::whereHas('service', function ($q) {
                $q->where('code', 'AS-IM4');
            })->count(),
            'all' => Demand::count(),
        ];
    }

    public function duplicateDemand()
    {
        return [
            //'plate' => PlateDuplicate::count(),
            //'gray_card' => GrayCardDuplicate::count(),
        ];
    }

    public function stats()
    {
        $stats = [];
        if (auth()->user()->hasRole(Roles::ADMIN)) {
            $stats += [
                'immatriculation_demand' => $this->immatriculationDemand(),
            ];

            $stats += [
                'duplicate_demand' => $this->duplicateDemand(),
            ];
        }

        $stats += [
            'plate' => (new PlateRepository)->stats()
        ];

        return $stats;
    }

    private function anattStats()
    {
        $dateFrom = request('date_from');
        if ($dateFrom) {
            $dateFrom = Carbon::createFromFormat('d-m-Y', $dateFrom)->format('Y-m-d');
        }

        $dateTo = request('date_to');
        if ($dateTo) {
            $dateTo = Carbon::createFromFormat('d-m-Y', $dateTo)->format('Y-m-d');
        }

        return [
            'total_demands' => Demand::when($dateFrom, fn($q) => $q->where('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->where('created_at', '<=', $dateTo))
                ->whereNotIn('demands.status', [Status::pending->name, Status::in_cart->name])
                ->count(),

            'total_2_wheels_vehicles' => Vehicle::whereRelation('category', 'nb_plate', '=', 1)
                ->whereHas('immatriculationDemand', function ($q) {
                    $q->whereNotIn('demands.status', [Status::pending->name, Status::in_cart->name]);
                })
                ->when($dateFrom, fn($q) => $q->where('vehicles.created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->where('vehicles.created_at', '<=', $dateTo))
                ->count(),

            'total_4_wheels_vehicles' => Vehicle::whereRelation('category', 'nb_plate', '=', 2)
                ->whereHas('immatriculationDemand', function ($q) {
                    $q->whereNotIn('demands.status', [Status::pending->name, Status::in_cart->name]);
                })
                ->when($dateFrom, fn($q) => $q->where('vehicles.created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->where('vehicles.created_at', '<=', $dateTo))
                ->count(),

            'total_immatriculations' => Immatriculation::when($dateFrom, fn($q) => $q->where('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->where('created_at', '<=', $dateTo))
                ->where('immatriculations.status', Status::validated->name)
                ->count(),

            'total_demands_by_service' => DB::table('demands')
                ->whereNotIn('demands.status', [Status::pending->name, Status::in_cart->name])
                ->join('services', 'services.id', '=', 'demands.service_id')
                ->select('services.name as service', DB::raw('COUNT(demands.id) as demand_count'))
                ->when($dateFrom, function ($query, $dateFrom) {
                    return $query->where('demands.created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query, $dateTo) {
                    return $query->where('demands.created_at', '<=', $dateTo);
                })
                ->groupBy('services.id', 'services.name')
                ->get(),

            'latest_immatriculed_vehicles' => DB::table('immatriculations')
                ->join('vehicles', 'vehicles.id', '=', 'immatriculations.vehicle_id')
                ->where('immatriculations.status', Status::validated->name)
                ->select('vehicles.vin', 'immatriculations.number_label')
                ->when($dateFrom, function ($query, $dateFrom) {
                    return $query->where('immatriculations.created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query, $dateTo) {
                    return $query->where('immatriculations.created_at', '<=', $dateTo);
                })
                ->orderBy('immatriculations.created_at', 'desc')
                ->limit(5)
                ->get(),

            'total_payments_per_month' => DB::table('transactions')
                ->selectRaw('EXTRACT(YEAR FROM created_at) AS year, EXTRACT(MONTH FROM created_at) AS month, SUM(amount) AS total_payments')
                ->whereIn('status', [Status::approved->name])
                ->when($dateFrom, function ($query, $dateFrom) {
                    return $query->where('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query, $dateTo) {
                    return $query->where('created_at', '<=', $dateTo);
                })
                ->groupByRaw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)')
                ->orderByRaw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)')
                ->get(),

            'latest_activities' => Activity::when($dateFrom, fn($q) => $q->where('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->where('created_at', '<=', $dateTo))
                ->with([
                    'causer:id,identity_id',
                    'causer.identity:id,firstname,lastname'
                ])
                ->latest()
                ->limit(20)
                ->get(),
        ];
    }
}
