<?php

namespace App\Repositories\Vehicle;

use App\Consts\Utils;
use App\Enums\Status;
use App\Http\Resources\ClientVehicleResource;
use App\Http\Resources\ProfileResource;
use App\Models\Config\Country;
use App\Models\Config\Park;
use App\Models\Config\Service;
use App\Models\Mutation;
use App\Models\Order\Demand;
use App\Models\Plate;
use App\Models\SaleDeclaration;
use App\Models\SimvebFile;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Models\Vehicle\VehicleType;
use App\Models\Vehicle\VehicleBrand;
use App\Models\Vehicle\VehicleEnergySource;
use App\Models\Vehicle\VehiclePassage;
use App\Repositories\Crud\AbstractCrudRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VehicleRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(Vehicle::class);
    }

    public function create()
    {
        return [
            'vehicle_types' => VehicleType::query()->select(['name', 'id'])->get(),
            'vehicle_brands' => VehicleBrand::query()->select(['name', 'description', 'id'])->get(),
            'vehicle_characteristics' => VehicleCharacteristicCategory::query()
                ->with(['vehicleCharacteristics'])
                ->select(['label', 'id', 'type'])
                ->get(),
            'energy_types' => VehicleEnergySource::query()->select(['id', 'name'])->get(),
            'countries' => Country::query()->select(['id', 'name'])->get(),
            'vehicle_categories' => VehicleCategory::query()->select(['id', 'name', 'nb_plate'])->get(),
            'parks' => Park::query()->select(['name', 'id', 'description', 'address'])->get(),
        ];
    }

    public function store(array $data, $request = null): Model|null
    {
        DB::beginTransaction();
        try {
            $vehicle = Vehicle::create($data);
            if (isset($data['vehicle_characteristics'])) {
                $vehicle->characteristics()->attach($data['vehicle_characteristics']);
            }

            DB::commit();
            return $vehicle;
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
            $vehicle = parent::update($vehicle, $data, $request);
            $vehicle->characteristics()->sync($data['vehicle_characteristics'] ?? []);

            DB::commit();
            return $vehicle;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function getVehicleByVin(string $vin): Model|Builder|null
    {
        return $this->model->newQuery()->where('vin', $vin)->first();
    }

    public function showDetails(array $data, $affiliate = false)
    {
        if (array_key_exists('vin', $data)) {
            $vehicle = $this->getVehicleByVin($data['vin']);
        } elseif (array_key_exists('immatriculation', $data)) {
            $vehicle = getVehicleByImmatriculation($data['immatriculation']);
        }

        $details = [
            'vehicle' => new ClientVehicleResource($vehicle),
            'plates' => Plate::whereIn('id', [$vehicle->front_plate_id, $vehicle->back_plate_id])->select(['id', 'serial_number', 'rfid', 'plate_color_id', 'plate_shape_id'])->with(['plateColor:id,label,color_code,text_color', 'plateShape:id,name'])->get(),
            'owner' => $vehicle->owner->load(['identity:id,firstname,lastname', 'institution:id,name,ifu']),
            'immatriculation' => $vehicle->immatriculation->load(['plateColor:id,label,color_code,text_color', 'frontPlateShape:id,name', 'backPlateShape:id,name'])
        ];

        if (!$affiliate) {
            $details = array_merge($details, [
                'passages' => VehiclePassage::query()->where('vehicle_id', $vehicle->id)->get()->map(function ($passage) {
                    [
                        'id' => $passage->id,
                        'vehicle_provenance' => $passage->vehicle_provenance_label,
                        'immatriculation_number' => $passage->immatriculation_number,
                        'vehicle_owner_firstname' => $passage->vehicle_owner_firstname,
                        'vehicle_owner_lastname' => $passage->vehicle_owner_lastname,
                        'driver_firstname' => $passage->driver_firstname,
                        'driver_lastname' => $passage->driver_lastname,
                        'driver_telephone' => $passage->driver_telephone,
                        'passage_type' => $passage->passage_type_label,
                        'created_at' => $passage->created_at,
                        'date' => $passage->created_date,
                        'officer' => (new ProfileResource($passage->load('officer'))),
                        'driving_license_photo' => $passage->driving_license_photo,
                        'gray_card_photo' => $passage->gray_card_photo,
                    ];
                }),
                'demands' => $vehicle->demands()
                    ->select([
                        'id',
                        'vehicle_id',
                        'service_id',
                        'reference',
                        'created_at',
                    ])
                    ->with([
                        'service:id,name,duration,type_id' => [
                            'type:id,code'
                        ],
                    ])->get(),
                'alerts' => $vehicle->alerts()->select(['id', 'vehicle_id', 'reference', 'authored_at', 'comment'])
                    ->where('vehicle_id', $vehicle->id)
                    ->with(['alertTypes:id,name', 'officer:id,identity_id,institution_id', 'officer.identity:id,firstname,lastname', 'officer.institution:id,name'])
                    ->latest()
                    ->get(),
                'pledges' => $vehicle->pledges()->select(['id', 'vehicle_id', 'created_at'])->get(),
                'lifecycle' => Service::whereIn('id', $vehicle->demands()->orderBy('created_at')->pluck('service_id')->toArray())->select(['id', 'code', 'name', 'created_at',])->get(),
                'actors' => $this->vehicleActorActions($vehicle),
                'images' => $vehicle->images,
                'files' => SimvebFile::where('type', SimvebFile::FILE)->where('model_type', Demand::class)->whereIn('model_id', $vehicle->demands()->pluck('id')->toArray())->get()
            ]);
        }

        return $details;
    }

    public function vehicleActorActions(Vehicle $vehicle)
    {
        $vehicle->load([
            'demands.demandActions.profile.identity',
            'demands.printOrders.author.identity',
            'demands.printOrders.platePrinter.identity',
            'demands.printOrders.cardPrinter.identity',
            'demands.printOrders.plateValidator.identity',
            'demands.printOrders.cardValidator.identity',
            'demands.printOrders.plateRejector.identity',
            'demands.printOrders.cardRejector.identity',
        ]);
        $actors = [];
        foreach ($vehicle->demands as $demand) {
            foreach ($demand->demandActions()->whereNotNull('done_at')->orderBy('created_at')->get() as $demandAction) {
                $permission = $demandAction->action->permissionService->permission;
                $actors[] = [
                    'actor' => $demandAction->profile?->identity?->fullName,
                    'action' => $permission->label . '. Référence demande: ' . $demand->reference,
                    'date' => Carbon::parse($demandAction->done_at)->translatedFormat(Utils::COMMON_DATE_FORMAT),
                ];
            }

            if ($demand->printOrders()->exists()) {
                foreach ($demand->printOrders as $printOrder) {
                    if ($printOrder->author) {
                        $actors[] = [
                            'actor' => $printOrder->author->identity->fullName,
                            'action' => 'Créer l\'ordre d\'impression de plaque. Référence ordre d\'impression: ' . $printOrder->reference,
                            'date' => Carbon::parse($printOrder->created_at)->translatedFormat(Utils::COMMON_DATE_FORMAT),
                        ];
                    }

                    if ($printOrder->platePrinter) {
                        $actors[] = [
                            'actor' => $printOrder->platePrinter->identity->fullName,
                            'action' => 'Imprimer la/les plaque·s. Référence ordre d\'impression: ' . $printOrder->reference,
                            'date' => Carbon::parse($printOrder->plate_printed_at)->translatedFormat(Utils::COMMON_DATE_FORMAT),
                        ];
                    }

                    if ($printOrder->cardPrinter) {
                        $actors[] = [
                            'actor' => $printOrder->cardPrinter->identity->fullName,
                            'action' => 'Imprimer la carte grise. Référence ordre d\'impression: ' . $printOrder->reference,
                            'date' => Carbon::parse($printOrder->card_printed_at)->translatedFormat(Utils::COMMON_DATE_FORMAT),
                        ];
                    }

                    if ($printOrder->plateValidator) {
                        $actors[] = [
                            'actor' => $printOrder->plateValidator->identity->fullName,
                            'action' => 'Valider l\'impression de plaque. Référence ordre d\'impression: ' . $printOrder->reference,
                            'date' => Carbon::parse($printOrder->validated_at)->translatedFormat(Utils::COMMON_DATE_FORMAT),
                        ];
                    }

                    if ($printOrder->cardValidator) {
                        $actors[] = [
                            'actor' => $printOrder->cardValidator->identity->fullName,
                            'action' => 'Valider l\'impression de carte grise. Référence ordre d\'impression: ' . $printOrder->reference,
                            'date' => Carbon::parse($printOrder->validated_at)->translatedFormat(Utils::COMMON_DATE_FORMAT),
                        ];
                    }

                    if ($printOrder->plateRejector) {
                        $actors[] = [
                            'actor' => $printOrder->plateRejector->identity->fullName,
                            'action' => 'Rejeter l\'impression de plaque. Référence ordre d\'impression: ' . $printOrder->reference,
                            'date' => Carbon::parse($printOrder->rejected_at)->translatedFormat(Utils::COMMON_DATE_FORMAT),
                        ];
                    }

                    if ($printOrder->cardRejector) {
                        $actors[] = [
                            'actor' => $printOrder->cardRejector->identity->fullName,
                            'action' => 'Rejeter l\'impression de carte grise. Référence ordre d\'impression: ' . $printOrder->reference,
                            'date' => Carbon::parse($printOrder->rejected_at)->translatedFormat(Utils::COMMON_DATE_FORMAT),
                        ];
                    }
                }
            }
        }

        foreach ($vehicle->pledges as $pledge) {
            $actors[] = [
                'actor' => $pledge->profile?->identity?->fullName,
                'action' => "Créer le gage",
                'date' => Carbon::parse($pledge->created_at)->translatedFormat(Utils::COMMON_DATE_FORMAT),
            ];
        }

        return collect($actors)
            ->groupBy('actor')
            ->map(function ($items, $key) {
                return [
                    'actor' => $key,
                    'actions' => $items->map(function ($item) {
                        return [
                            'action' => $item['action'],
                            'date' => $item['date']
                        ];
                    })->toArray()
                ];
            })->values()->toArray();
    }

    public function getClientVehicles()
    {
        $query = $this->model->newQuery()->where('is_transformed', false);

        if (request()->isNotFilled('key')) {
            if (getOnlineProfile()->isUserProfile()) {
                $condition = ['profile_id' => getOnlineProfile()->id];
            } else {
                $condition = ['institution_id' => getOnlineProfile()?->institution_id];
            }
            $query = $query->whereHas('owner', fn($query) => $query->where($condition));
        } else {
            $query->whereHas('owner', function ($q) {
                $q->whereRelation('identity', 'npi', '=', request('key'))
                    ->orWhereRelation('institution', 'ifu', '=', request('key'));
            });
        }

        return $query->get();
    }

    public function getClientBoughtVehicles()
    {
        $query = getOnlineProfile()->demands()->where('model_type', Mutation::class)->whereIn('status', [Status::validated->name, Status::closed->name])
            ->with([
                'vehicle',
                'vehicle.immatriculation',
                'vehicle.brand',
                'vehicle.demands' => function ($demandsQuery) {
                    $demandsQuery->where('model_type', SaleDeclaration::class)->with([
                        'model' => function ($modelQuery) {
                            $modelQuery->where('new_owner_npi', getOnlineProfile()?->identity?->npi)
                                ->orWhere('new_owner_ifu', getOnlineProfile()?->institution?->ifu);
                        },
                        'model.certificate'
                    ])->first();
                }
            ]);

        return $query?->paginate(request()->input('per_page', 15));
    }
}
