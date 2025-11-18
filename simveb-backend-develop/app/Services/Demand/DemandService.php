<?php

namespace App\Services\Demand;

use App\Consts\AvailableServiceType;
use App\Enums\ProfileTypesEnum;
use App\Enums\DemandUpdatesTypeEnum;
use App\Enums\ProcessTypeEnum;
use App\Enums\Status;
use App\Exceptions\UnknownServiceException;
use App\Models\Config\Service;
use App\Models\Space\Space;
use App\Models\DemandOtp;
use App\Models\Order\Cart;
use App\Models\Order\Demand;
use App\Models\Order\Order;
use App\Models\Order\Transaction;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use App\Models\SimvebFile;
use App\Repositories\Crud\CrudRepository;
use App\Repositories\Demand\DemandRepository;
use App\Repositories\Vehicle\VehicleOwnerRepository;
use App\Repositories\Vehicle\VehicleRepository;
use App\Services\Treatment\AssignTreatmentService;
use App\Services\TreatmentTimeService;
use App\Services\VehicleOwnerService;
use App\Services\VehicleService;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ntech\RequiredDocumentPackage\Models\DocumentType;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DemandService
{
    use UploadFile;

    private VehicleRepository $vehicleRepository;
    private VehicleOwnerRepository $vehicleOwnerRepository;
    private CrudRepository $spaceRepository;
    private CrudRepository $cartRepository;
    private VehicleOwnerService $vehicleOwnerService;
    private VehicleService $vehicleService;
    private DemandRepository $repository;
    private CrudRepository $orderRepository;
    private CrudRepository $transactionRepository;
    private AssignTreatmentService $assignTreatmentService;

    public function __construct()
    {
        $this->repository = new DemandRepository(Demand::class);
        $this->vehicleRepository = new VehicleRepository;
        $this->vehicleOwnerRepository = new VehicleOwnerRepository;
        $this->spaceRepository = new CrudRepository(Space::class);
        $this->cartRepository = new CrudRepository(Cart::class);
        $this->orderRepository = new CrudRepository(Order::class);
        $this->vehicleOwnerService = new VehicleOwnerService;
        $this->vehicleService = new VehicleService;
        $this->transactionRepository = new CrudRepository(Transaction::class);
        $this->assignTreatmentService = new AssignTreatmentService;
    }

    /**
     * @param array $data
     * @return Model
     */
    public function store(array $data, $request)
    {
        $serviceExists = Service::where('id', $data['service_id'])
            ->where('code', AvailableServiceType::VEHICLE_TRANSFORMATION)
            ->exists();

        DB::beginTransaction();
        try {
            $onlineProfile = getOnlineProfile();

            if ($onlineProfile->type->code == ProfileTypesEnum::company->name) {
                $vehicleOwner = $this->vehicleOwnerService->storeOrGetVehicleOwner(null, $onlineProfile->institution->ifu, $onlineProfile);
            } else {
                $vehicleOwner = $this->vehicleOwnerService->storeOrGetVehicleOwner($request->npi, $request->ifu);
            }

            $vehicle = $this->vehicleService->getVehicleByVinOrImmatriculation(["vin" => $request->vin]);

            $vehicle->update([
                'owner_id' => $vehicleOwner->id,
            ]);

            $data['vehicle_id'] = $vehicle->id;
            $data['vehicle_owner_id'] = $vehicleOwner->id;

            $demand = $this->repository->storeOrUpdate(Arr::only($data, [
                'vehicle_id',
                'service_id',
            ]), Arr::only($data, [
                'institution_id',
                'vehicle_owner_id',
                'vehicle_id',
                'profile_id',
                'service_id',
            ]), $serviceExists);

            if ($request->filled('authorization_id')) {
                DemandOtp::query()
                    ->where('id', $request->authorization_id)
                    ->update([
                        'model_id' => $demand->id,
                        'model_type' => $demand::class,
                    ]);
                $demand->update(['otp_verified' => true]);
            }

            if ($request->documents) {
                foreach ($request->documents as $document) {
                    $demand->files()->whereHas('fileType', function ($q) use ($document) {
                        $q->where('document_types.id', $document['type_id']);
                    })->delete();
                    $fileInfo = $this->saveFile($document['file'], Str::slug($demand->service->code));
                    $demand->files()->create([
                        'path' => $fileInfo,
                        'type' => SimvebFile::FILE,
                        'file_type_id' => $document['type_id'],
                    ]);
                }
            }

            $adapter = getDemandAdapter($demand->service);
            $adapter->initDemand($demand, $data);

            DB::commit();
            return $demand->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }


    public function update(Demand $demand, array $data, $request)
    {
        DB::beginTransaction();
        try {
            if ($request->documents || isset($data['documents'])) {
                foreach ($request->documents as $document) {
                    $oldDocument = $demand->files()->whereHas('fileType', function ($q) use ($document) {
                        $q->where('document_types.id', $document['type_id']);
                    })->first();

                    $newFileInfo = $this->saveFile($document['file'], Str::slug($demand->service->code));

                    $newDocument = $demand->files()->create([
                        'path' => $newFileInfo,
                        'type' => SimvebFile::FILE,
                        'file_type_id' => $document['type_id'],
                    ]);

                    saveDemandUpdateHistory([
                        'old_value' => $oldDocument->url,
                        'new_value' => $newDocument->url,
                        'type' => DemandUpdatesTypeEnum::attachments->name,
                        'demand_id' => $demand->id,
                        'model_type' => DocumentType::class,
                        'model_id' => $document['type_id'],
                    ]);

                    $oldDocument->delete();
                }
                unset($data['documents']);
            }
            if (!empty($data)) {
                $adapter = getDemandAdapter($demand->service);
                $adapter->update($demand, $data);
            }
            $this->repository->update($demand, ['updates_status' => Status::pending->name]);

            DB::commit();
            return $demand->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }


    public function create()
    {
        return [
            'spaces' => Space::query()->get(),
            'required_documents' => [], //Demand::requiredDocumentTypes(),
            'plate_colors' => PlateColor::query()->select(['label', 'id'])->get(),
            'plate_shapes' => PlateShape::query()->select(['name', 'id'])->get(),
        ] + $this->vehicleOwnerRepository->create() + $this->vehicleRepository->create();
    }

    /**
     * @throws ValidationException
     * @throws UnknownServiceException
     */
    public function addDemandToCart(Demand $demand): Model
    {
        if ($demand->otp && !$demand->otp_verified) {
            abort(ResponseAlias::HTTP_BAD_REQUEST, __('errors.otp_not_verified'));
        }

        $cart = getCart();
        if ($demand->status == Status::pending->name && !in_array($demand->id, $cart->demands()->pluck('id')->toArray())) {
            $totalAmount = $demand->getCost() + $cart->amount;
            $cart->demands()->attach($demand->id, ['amount' => $demand->getCost()]);
            $cart->update([
                'amount' => $totalAmount,
                'status' => Status::submitted->name
            ]);
            $demand->update([
                'status' => Status::in_cart->name
            ]);
        }

        return $cart->refresh();
    }

    /**
     * @throws ValidationException
     * @throws UnknownServiceException
     */
    public function validateCart(): Model
    {
        DB::beginTransaction();
        try {
            $cart = getCart();
            $data = [
                'amount' => $cart->amount,
                'institution_id' => $cart->institution_id,
                'profile_id' => $cart->profile_id,
            ];

            if ($cart->institution_id && $cart->status != Status::approved->name && getOnlineProfile()?->type->code == ProfileTypesEnum::company->name) {
                abort(ResponseAlias::HTTP_FORBIDDEN, "Désolé ce panier doit être approuvé au préalable.");
            }

            $order = $this->orderRepository->store($data);
            foreach ($cart->demands()->get() as $demand) {
                $order->demands()->attach($demand->id, ['amount' => $demand->getCost()]);
            }
            $order->transaction()->create([
                'fees' => 0,
                'amount' => $order->amount,
                'total_amount' => $order->amount,
                'model_id' => $order->id,
                'model_type' => $order::class,
            ]);
            $cart->update([
                'status' => Status::validated->name
            ]);

            DB::commit();
            return $order->load([
                'demands:id,reference,model_id,model_type,service_id' => [
                    'service:id,name,type_id' => ['type:id,code']
                ]
            ])->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function getOwnerDemands($vehicleOwnerId)
    {
        return $this->repository
            ->model
            ->newQuery()
            ->with(['vehicle.characteristics', 'vehicleOwner', 'extraServices:id,name,description,cost'])
            ->where('vehicle_owner_id', $vehicleOwnerId)
            ->get();
    }

    /**
     * @throws UnknownServiceException
     */
    public function submitDemand(Demand $demand)
    {
        $adapter = getDemandAdapter($demand->service);
        $treatment = $demand->treatments()->create();

        $demand->update([
            'status' => Status::submitted->name,
            'submitted_at' => now(),
            'active_treatment_id' => $treatment->id
        ]);
        try {
            (new TreatmentTimeService)->startTreatmentTime($treatment, Status::submitted->name);
        } catch (\Exception $e) {
            Log::debug($e);
        }
        $adapter->submit($demand);

        $this->assignTreatmentService->assignDemandToCenter($demand);

        return $demand;
    }

    public function approveCart()
    {
        $cart = getCart();
        DB::beginTransaction();
        try {
            $cart->update([
                'status' => Status::approved->name
            ]);
            $data = [
                'amount' => $cart->amount,
                'institution_id' => $cart->institution_id,
                'profile_id' => $cart->profile_id,
            ];

            $order = $this->orderRepository->store($data);

            foreach ($cart->demands()->get() as $demand) {
                $order->demands()->attach($demand->id, ['amount' => $demand->getCost()]);
            }

            $order->transaction()->create([
                'fees' => 0,
                'amount' => $order->amount,
                'total_amount' => $order->amount,
                'model_id' => $order->id,
                'model_type' => $order::class,
            ]);

            $cart->update([
                'status' => Status::validated->name
            ]);

            DB::commit();

            return $order->load([
                'demands:id,reference,model_id,model_type,service_id' => [
                    'service:id,name,type_id' => ['type:id,code']
                ]
            ])->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function closeDemand(Demand $demand)
    {
        $candidates = getProfileByPermissions(getDemandActionPermission(getActionToPerformOnDemandByStatus($demand, Status::pre_validated->name)));
    }

    public function close(array $data)
    {
        DB::beginTransaction();
        try {
            $demand = Demand::find($data['demand_id']);

            $demand->update(['status' => Status::closed->name]);

            $demand->activeTreatment->update([
                'closed_at' => now(),
                'closed_by' => getOnlineProfile()->id,
            ]);

            // TODO: update status of demand model

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
}
