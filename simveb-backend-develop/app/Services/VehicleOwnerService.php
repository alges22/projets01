<?php

namespace App\Services;

use App\Enums\LegalStatusEnum;
use App\Http\Requests\Vehicle\VehicleOwnerRequest;
use App\Http\Resources\VehicleOwnerResource;
use App\Models\Auth\Profile;
use App\Models\Config\Country;
use App\Models\Institution\Institution;
use App\Models\Vehicle\VehicleOwner;
use App\Repositories\Vehicle\VehicleOwnerRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ntech\UserPackage\Models\Identity;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VehicleOwnerService
{
    private VehicleOwnerRepository $ownerRepository;

    public function __construct()
    {
        $this->ownerRepository = new VehicleOwnerRepository;
    }

    public function getOwnerByNpi($npi, $fromResource = true)
    {
        $identity = Identity::where('npi', $npi)->first();
        if ($identity){
            $ownerData = VehicleOwner::query()->where('identity_id', $identity->id)->first();
            $ownerData->load($ownerData::relations());
            $person = new VehicleOwnerResource($ownerData, 'database');
        }else{
            $response = (new IdentityService)->showByNpi($npi)->response()->getData(true)['data'];
            if (!isset($response['npi'])) {
                throw new \Exception('Error: Cannot find owner.');
            }
            $ownerData = collect($response);
            $person = new VehicleOwnerResource($response, 'api') ;
        }

        return $fromResource ? $person : $ownerData;
    }

    public function getOwnerByIfu($ifu)
    {
        $identity = Institution::where('ifu',$ifu)->first();

        if ($identity) {
            $ownerData = VehicleOwner::where('institution_id',$identity->id)->first();

            if (!isset($ownerData->institution->email)) {
                throw new \Exception('Error: Cannot find owner.');
            }

            return new VehicleOwnerResource($ownerData, 'database');
        } else {
            $response = (new IdentityService)->getIdentityByIfu($ifu);

            if (!isset($response['email'])) {
                throw new \Exception('Error: Cannot find owner');
            }

            return new VehicleOwnerResource($response, 'api') ;
        }
    }


    public function storeOrGetVehicleOwnerByNPI(string $npi)
    {
        try
        {
            $identity = Identity::where('npi', $npi)->first();
            $profile = Profile::where('identity_id', $identity?->id)->first();
            $data = [
                'identity_id' => $identity?->id,
                'profile_id' => $profile?->id,
                'legal_status' => LegalStatusEnum::physical->name
            ];
            $owner = $profile?->vehicleOwner;
            if ($owner == null){
                $owner = $this->ownerRepository->store($data);
            }else{
                $owner = $this->ownerRepository->update($owner, $data);
             }

            return $owner;
        }catch (\Exception $exception){
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }
    public function storeOrGetVehicleOwnerByIFU(string $ifu, Profile $profile = null)
    {
        try
        {
            $data = [
                'institution_id' => Institution::query()->where('ifu',$ifu)->first()->id,
                'legal_status' => LegalStatusEnum::moral->name,
                'profile_id' => $profile?->id,
            ];
            $npiAndIfu = ['npi' => null, 'ifu' => $ifu];

            $owner = $this->ownerRepository->getOwner($npiAndIfu, $data['institution_id'] ?? null, $profile);
            if ($owner == null){
                $owner = $this->ownerRepository->store($data);
            }

            return $owner;
        }catch (\Exception $exception){
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }


    public function saveCountry($name)
    {
        return Country::create([
            'name' => $name,
            'description' => null,
            'native_country' => null,
        ]);
    }


    public function storeOrGetVehicleOwner(?string $npi, ?string $ifu, Profile $profile = null) : Builder|Model
    {
        if ($npi){
            $vehicleOwner = $this->storeOrGetVehicleOwnerByNPI($npi);
        }else{
            $vehicleOwner = $this->storeOrGetVehicleOwnerByIFU($ifu, $profile);
        }

        return $vehicleOwner;
    }

    public function getVehicleOwner(array $data) : JsonResource|array
    {
        if (isset($data['npi'])){
            $vehicleOwner = $this->getOwnerByNpi($data['npi']);
        } elseif (isset($data['ifu'])){
            $vehicleOwner = $this->getOwnerByIfu($data['ifu']);
        } else{
            $vehicleOwner = null;
        }

        return $vehicleOwner;
    }

}
