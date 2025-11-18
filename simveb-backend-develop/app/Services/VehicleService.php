<?php

namespace App\Services;

use App\Enums\CharacteristicCategoriesEnum;
use App\Enums\ReimmatriculationReasonEnum;
use App\Enums\Status;
use App\Http\Resources\ClientVehicleResource;
use App\Models\Auction\AuctionSaleDeclaration;
use App\Models\Config\Country;
use App\Models\Config\Park;
use App\Models\Config\ReimmatriculationReason;
use App\Models\Motorcycle;
use App\Models\Reform\ReformDeclaration;
use App\Models\Vehicle\GmaVehicle;
use App\Models\Vehicle\GmdVehicle;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleBrand;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Models\Vehicle\VehicleOwner;
use App\Repositories\MotorcycleRepository;
use App\Repositories\Vehicle\VehicleRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ntech\UserPackage\Models\Identity;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VehicleService
{

    public VehicleRepository $vehicleRepository;

    public function __construct()
    {
        $this->vehicleRepository = new VehicleRepository;
    }

    public function create()
    {
        return [];
    }

    public function showVehicleByvin(array $vehicleData)
    {
        try {
            $data = ['success' => true];
            $vehicle = Vehicle::where('vin', $vehicleData['vin'])->first();
            if ($vehicle) {
                $data['vehicle'] = new ClientVehicleResource($vehicle->load($vehicle::relations()));
            } else {
                $response = Http::get(config('app.sandbox_host') . '/vehicles/' . $vehicleData['vin']);
                if ($response->successful()) {
                    $data['vehicle']  =  new ClientVehicleResource($response->json(), 'api');
                } else {
                    $response = Http::get(config('app.sandbox_host') . '/motorcycles/' . $vehicleData['vin']);
                    if ($response->successful()) {
                        $data['vehicle']  =  new ClientVehicleResource($response->json(), 'api');
                    } else {
                        $data['success'] = false;
                        $data['message'] = $response->reason();
                    }
                }
            }

            return $data;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    /**
     * @param array $data
     * @return Model|void|null
     */
    public function storeOrGetVehicleByVin(array $data)
    {
        DB::beginTransaction();
        try {
            if (!$vehicle = $this->vehicleRepository->findWhere(['vin' => $data['vin']])) {
                $response = Http::get(config('app.sandbox_host') . '/vehicles/' . $data['vin']);
                if ($response->failed()) {
                    $response = Http::get(config('app.sandbox_host') . '/motorcycles/' . $data['vin']);
                    if ($response->failed()) {
                        abort(ResponseAlias::HTTP_NOT_FOUND, 'Véhicule introuvable');
                    }
                }
                $data = json_decode($response->body());
                $country = Country::where('name', $data->origin_country)->first();
                $vehicleBrand = $this->manageBrand($data->vehicle_brand);
                $park = $this->managePark($data->park);


                $category = $data->is_car ? VehicleCategory::query()->where('nb_plate', 2)->first() : VehicleCategory::query()->where('nb_plate', 1)->first();
                $vehicleData = [
                    'vehicle_category_id' => $category->id,
                    'origin_country_id' => $country->id ?? Country::first()->id,
                    'customs_reference' => $data->customs_reference ?? Str::random(),
                    'vin' => $data->vin,
                    'vehicle_brand_id' => $vehicleBrand->id,
                    'vehicle_model' => $data->vehicle_model,
                    'number_of_seats' => $data->number_of_seats,
                    'engin_number' => $data->engin_number,
                    'first_circulation_year' => $data->first_circulation_year,
                    'park_id' => $park->id,
                ];

                $vehicle = $this->vehicleRepository->store($vehicleData);

                $motorcycle = Motorcycle::where('vin', $data->vin);
                if (!$data->is_car && $motorcycle) {
                    $motorcycle->update( ['vehicle_id' => $vehicle->id]);
                }

                foreach (VehicleCharacteristicCategory::whereIn('field_name', CharacteristicCategoriesEnum::toArray())->get() as $category) {
                    $characteristic = VehicleCharacteristic::updateOrCreate([
                        'category_id' => $category->id,
                        'value' => $data->{$category->field_name} ?? null
                    ]);

                    if ($existingCharacteristic = $vehicle->characteristics()->where('category_id', $category->id)->first()) {
                        $vehicle->characteristics()->detach($existingCharacteristic);
                    }

                    $vehicle->characteristics()->attach($characteristic);
                }
            }

            DB::commit();
            return $vehicle->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    /**
     * @param array $data
     * @return bool
     */
    public function checkVehicleExists(array $data): bool
    {
        try {
            $response = Http::get(config('app.sandbox_host') . '/vehicles/' . $data['vin']);

            return $response->successful();
        } catch (\Exception $exception) {
            Log::debug($exception);
            return false;
        }
    }

    /**
     * @param array $data
     * @return bool
     */
    public function checkMotorcycleExists(array $data): bool
    {
        try {
            $response = Http::get(config('app.sandbox_host') . '/motorcycles/' . $data['vin']);

            return $response->successful();
        } catch (\Exception $exception) {
            Log::debug($exception);
            return false;
        }
    }

    public function manageBrand($name)
    {
        $data = VehicleBrand::where('name', $name)->first();
        if (!$data) {
            $data = VehicleBrand::create([
                'name' => $name,
                'description' => null,
                'native_country' => null,
            ]);
        }

        return $data;
    }

    public function managePark($name)
    {
        $data = Park::where('name', $name)->first();
        if (!$data) {
            $data = Park::create([
                'name' => $name,
                'description' => null,
                'native_country' => null,
            ]);
        }

        return $data;
    }

    public function getVehicleByVinOrImmatriculation(array $data): ?Model
    {
        return $this->storeOrGetVehicleByVin($data);
    }

    public function verifyVehicleSituation(string $vin)
    {
        $vehicle = $this->vehicleRepository->findWhere(['vin' => $vin]);

        if (!$vehicle) {
            return [false, ['message' => 'Véhicule introuvable.']];
        }

        $columns = ['id', 'title', 'code', 'requires_reason', 'requires_title_deposit', 'requires_transfer_certificate', 'enable_plate_transformation', 'img_path'];

        if (ReformDeclaration::whereHas('reformedVehicles', fn ($q) => $q->where('vehicle_id', $vehicle->id))->exists()) {
            $data['reimmatriculation_reason'] = ReimmatriculationReason::where('code', 'M-' . ReimmatriculationReasonEnum::RF->name)->select($columns)->first();
        } elseif (AuctionSaleDeclaration::whereHas('saledVehicles', fn ($q) => $q->where('vehicle_id', $vehicle->id))->exists()) {
            $data['reimmatriculation_reason'] = ReimmatriculationReason::where('code', 'M-' . ReimmatriculationReasonEnum::VE->name)->select($columns)->first();
        } elseif (GmaVehicle::where('vehicle_id', $vehicle->id)->where('status', Status::validated->name)->exists()) {
            $data['reimmatriculation_reason'] = ReimmatriculationReason::where('code', 'M-' . ReimmatriculationReasonEnum::OI->name)->select($columns)->first();
        } elseif (GmdVehicle::where('vehicle_id', $vehicle->id)->where('status', Status::validated->name)->exists()) {
            $data['reimmatriculation_reason'] = ReimmatriculationReason::where('code', 'M-' . ReimmatriculationReasonEnum::D->name)->select($columns)->first();
        } else {
            $data['reimmatriculation_reason'] = ReimmatriculationReason::where('code', 'M-' . ReimmatriculationReasonEnum::AC->name)->select($columns)->first();
        }

        $data['vehicle'] = new ClientVehicleResource($vehicle->load($vehicle::relations()));

        return [true, $data];
    }
    public function getVehiclesByCustomsReference(array $data): array
    {
        try {
            $response = Http::get(config('app.sandbox_host') . '/vehicles/reference/' . $data['customs_reference']);

            if ($response->failed()) {
            $response = Http::get(config('app.sandbox_host') . '/motorcycles/reference/' . $data['customs_reference']);
                if ($response->failed()) {
                    abort(ResponseAlias::HTTP_NOT_FOUND, 'Véhicule introuvable');
                }
            }

            return $response->json();
        } catch (\Exception $exception) {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('une erreure est survenue'));
        }
    }

    /*public function getVehiclesByNpiOrIfu($data)
    {
        $identityId = Identity::select('id')->where('npi', $data->npiOrIfu)
            ->orWhere('ifu', $data->npiOrIfu)
            ->first();
        $ownerId = VehicleOwner::where('identity_id', $identityId)->first();
        $vehicles = Vehicle::where('owner_id', $ownerId);

    }*/
}
