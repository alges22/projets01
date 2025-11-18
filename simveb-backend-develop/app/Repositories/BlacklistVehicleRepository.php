<?php

namespace App\Repositories;

use App\Enums\Status;
use App\Jobs\ImportBlacklistVehicleJob;
use App\Models\Config\BlacklistVehicle;
use App\Models\Vehicle\Vehicle;
use App\Repositories\Crud\AbstractCrudRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BlacklistVehicleRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(BlacklistVehicle::class);
    }

    /**
     *
     */
    public function store(array $data, $request = null): ?Model
    {
        DB::beginTransaction();

        try {
            $vehicle = Vehicle::where('vin', $data['vin'])->first();
            $data['author_id'] = getOnlineProfile()->id;
            $data['vehicle_id'] = $vehicle?->id;
            $blacklistVehicle = parent::store($data);

            DB::commit();

            return $blacklistVehicle;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * @return mixed
     */
    public function validate(BlacklistVehicle $blacklistVehicle)
    {
        DB::beginTransaction();

        try {

            if ($blacklistVehicle->validated_at) {
                return [false, ['message' => "Ce vehicule a déja été approuvé dans la liste noire", 'code' => Response::HTTP_CONFLICT]];
            }

            $data['validator_id'] = auth()->user()->onlineProfile->id;
            $data['validated_at'] = now();
            $data['status'] = Status::validated->name;
            $blacklistVehicle = parent::update($blacklistVehicle, $data);

            DB::commit();

            return $blacklistVehicle;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * @return mixed
     */
    public function reject(BlacklistVehicle $blacklistVehicle)
    {
        DB::beginTransaction();

        try {

            if ($blacklistVehicle->rejected_at) {
                return [false, ['message' => "Ce vehicule a déja été rejeté de la liste noire", 'code' => Response::HTTP_CONFLICT]];
            }

            $data['rejector_id'] = auth()->user()->onlineProfile->id;
            $data['rejected_at'] = now();
            $data['status'] = Status::rejected->name;
            $blacklistVehicle = parent::update($blacklistVehicle, $data);

            DB::commit();

            return $blacklistVehicle;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function import(array $data)
    {
        $filePath = $this->saveFile($data['file'], 'to-import/blacklist-vehicles');

        ImportBlacklistVehicleJob::dispatch($filePath['path'], getOnlineProfile());

        return ['message' => 'Enregistrement des véhicules sur la liste noire en cours!'];
    }
}
