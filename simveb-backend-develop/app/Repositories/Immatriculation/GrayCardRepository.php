<?php

namespace App\Repositories\Immatriculation;

use App\Consts\AvailableServiceType;
use App\Consts\Roles;
use App\Enums\Status;
use App\Models\Account\User;
use App\Models\GrayCard;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Order\Demand;
use App\Models\Vehicle\VehicleOwner;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Traits\UserDataTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GrayCardRepository extends AbstractCrudRepository
{
    use UserDataTrait;

    public function __construct()
    {
        parent::__construct(GrayCard::class);
    }

    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        $query = $this->model->newQuery()
            ->where('status', '<>', Status::pending->name)
            ->with(['vehicleOwner.identity:id,lastname,firstname,email,telephone']);

        if (Auth::user()->hasRole([Roles::SERVICE_HEADER])) {
            $query =  $query->whereHas(
                'activeTreatment',
                fn($query) => $query->whereIn('service_id', $this->staff()->services()->pluck('id')->toArra())
            );
        }

        return $query->paginate(request()->input('per_page', 15));
    }

    public function search()
    {
        $keyword = strtoupper((request()->get('keyword')));
        $field = request()->get('field');
        $vehicleOwner = VehicleOwner::where('identity_id', User::find(request('user_id'))->identity_id)->first();

        if ($field == 'immatriculation') {
            $immatriculation = Immatriculation::whereHas('immatriculationDemand', function ($q) use ($vehicleOwner) {
                $q->where('vehicle_owner_id', $vehicleOwner->id);
            })->where('number_label', 'like', "%$keyword%")->select(['id'])->first();

            $result = $immatriculation ? $immatriculation->grayCard->id : null;
        } else {
            $result = GrayCard::where('vehicle_owner_id', $vehicleOwner->id)->where('number', 'like', "%$keyword%")->first()?->id;
        }

        return ['id' => $result];
    }

    public function storeAfterDemandValidation(Demand $demand)
    {
        DB::beginTransaction();
        try {
            switch ($demand->service->type->code) {
                case AvailableServiceType::GRAY_CARD_DUPLICATE: {
                        $oldGrayCard = $demand->model->oldGrayCard;

                        $newGrayCard = GrayCard::create([
                            'immatriculation_id' => $oldGrayCard->immatriculation_id,
                            'number' => GrayCard::generateNumber(),
                            'vehicle_id' => $oldGrayCard->vehicle_id,
                            'vehicle_owner_id' => $oldGrayCard->vehicle_owner_id,
                            'is_duplicate' => true,
                        ]);

                        $demand->model->update(['new_gray_card_id' => $newGrayCard->id]);
                        break;
                    }
                case AvailableServiceType::VEHICLE_TRANSFORMATION: {
                        $vehicleTransformation = $demand->model;

                        GrayCard::create([
                            'immatriculation_id' => $vehicleTransformation->vehicle->frontPlate ? $vehicleTransformation->vehicle->frontPlate->immatriculation_id : $vehicleTransformation->vehicle->backPlate->immatriculation_id,
                            'number' => GrayCard::generateNumber(),
                            'vehicle_id' => $vehicleTransformation->vehicle_id,
                            'vehicle_owner_id' => $vehicleTransformation->vehicle_owner_id,
                        ]);
                        break;
                    }
                default: {
                        $demandModel = $demand->model;

                        $immatriculationId = match ($demand->service->type->code) {
                            AvailableServiceType::IMMATRICULATION_STANDARD,
                            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
                            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL => $demandModel->id,
                            default => $demandModel->immatriculation_id,
                        };

                        $vehicleId = match ($demand->service->type->code) {
                            AvailableServiceType::IMMATRICULATION_STANDARD,
                            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
                            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
                            AvailableServiceType::RE_IMMATRICULATION => $demandModel->vehicle_id,
                            default => $demandModel->immatriculation->vehicle_id,
                        };

                        $vehicleOwnerId = match ($demand->service->type->code) {
                            AvailableServiceType::IMMATRICULATION_STANDARD,
                            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
                            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
                            AvailableServiceType::RE_IMMATRICULATION => $demandModel->vehicle_owner_id,
                            default => $demandModel->immatriculation->vehicle_owner_id,
                        };

                        GrayCard::create([
                            'immatriculation_id' => $immatriculationId,
                            'number' => GrayCard::generateNumber(),
                            'vehicle_id' => $vehicleId,
                            'vehicle_owner_id' => $vehicleOwnerId,
                        ]);
                        break;
                    }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
