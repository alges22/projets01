<?php

namespace App\Services;

use App\Consts\Utils;
use App\Enums\InstitutionTypesEnum;
use App\Http\Requests\Vehicle\VehicleOwnerRequest;
use App\Http\Resources\IdentityResource;
use App\Models\Space\Space;
use App\Models\Config\Country;
use App\Models\Institution\Institution;
use App\Models\Institution\InstitutionType;
use App\Repositories\InstitutionRepository;
use App\Repositories\Vehicle\VehicleOwnerRepository;
use App\Services\External\AnipService;
use App\Services\External\DGIService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ntech\UserPackage\Models\Identity;
use Ntech\UserPackage\Repositories\IdentityRepository;
use Ntech\UserPackage\Services\UserService;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class IdentityService
{
    private UserService $userService;
    private VehicleOwnerRepository $ownerRepository;
    private IdentityRepository $identityRepository;
    public function __construct()
    {
        $this->userService = new UserService;
        $this->ownerRepository = new VehicleOwnerRepository;
        $this->identityRepository = new IdentityRepository;
    }

    public function showByNpi(string $npi)
    {
        try {
            $identity = Identity::where('npi', $npi)->first();
            if ($identity) {
                return new IdentityResource($identity);
            } else {
                if (app()->environment('local') || app()->environment('dev') || in_array($npi, Utils::LOCAL_NPI)) {
                    $response = Http::get(config('app.sandbox_host') . '/persons/' . $npi)->json();
                } else {
                    $response = (new AnipService)->getPerson($npi);
                }

                return new IdentityResource($response, 'api');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function getIdentityByNpi(string $npi)
    {
        try {
            $identity = Identity::where('npi', $npi)->first();
            $data = null;
            if ($identity) {
                $data =  [
                    'npi' => $npi,
                    "firstname" => $identity->firstname,
                    "lastname" => $identity->lastname,
                    "email" => $identity->email,
                    "telephone" => $identity->telephone,
                ];
            } else {
                if (app()->environment('local') || app()->environment('dev') || in_array($npi, Utils::LOCAL_NPI)) {
                    $response = Http::get(config('app.sandbox_host') . '/persons/' . $npi)->json();
                } else {
                    $response = (new AnipService)->getPerson($npi);
                }
                if (isset($response['npi'])) {
                    $data = [
                        'npi' => $npi,
                        "firstname" => $response['firstname'],
                        "lastname" => $response['lastname'],
                        "email" => $response['email'],
                        "telephone" => $response['phone_number'],
                    ];
                }
            }
            return $data ? (object) $data: null;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
    
    public function getIdentityByIfu(string $ifu, $toObject = false)
    {
        try {
            $institution = Institution::where('ifu', $ifu)->first();
            $data = null;
            if ($institution) {
                $data =  [
                    'ifu' => $ifu,
                    "name" => $institution->name,
                    "email" => $institution->email,
                    "telephone" => $institution->telephone,
                ];
            } else {
                if (app()->environment('local') || app()->environment('dev') || in_array($ifu, Utils::LOCAL_IFU)) {
                    $response = Http::get(config('app.sandbox_host') . '/companies/' . $ifu)->json();
                } else {
                    $response = (new DGIService)->getCompanyByIFU($ifu);
                }
                if (isset($response['object']['ifu'])) {
                    $data = [
                        'ifu' => $ifu,
                        "name" => $response['object']['raisonSociale'] ?? null,
                        "email" => $response['object']['email'] ?? null,
                        "telephone" => $response['object']['telephone'] ?? null,
                    ];
                }
            }
            return $toObject ? (object) $data : $data;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function getIdentites(string $npis)
    {
        $npis = json_decode($npis);
        if (!$npis) {
            return [];
        }
        $identities = [];

        foreach ($npis as $npi) {
            $identities[$npi] = $this->showByNpi($npi);
        }

        return $identities;
    }

    public function showByIfu(array $data)
    {
        $institution = Institution::where('ifu', $data['ifu'])->first();
        if (!$institution) {
            if (app()->environment('local') || app()->environment('dev') || in_array($data['ifu'], Utils::LOCAL_IFU)) {
                $response = Http::get(config('app.sandbox_host') . '/companies/' . $data['ifu'])->json();
            } else {
                $response = (new DGIService)->getCompanyByIFU($data['ifu']);
            }
            if (isset($response['object']['ifu'])) {
                $institution = $response['object'];
            }
        }

        return $institution;
    }


    public function storeByNpi(VehicleOwnerRequest $request)
    {
        DB::beginTransaction();
        try {
            if (app()->environment('local') || app()->environment('dev') || in_array($request->npi, Utils::LOCAL_NPI)) {
                $data = Http::get(config('app.sandbox_host') . '/persons/' . $request->npi)->json();
            } else {
                $data = (new AnipService)->getPerson($request->npi);
            }

            if (!isset($data['npi'])) {
                return response()->json(['error' => 'Propriétaire introuvable'], $data->status());
            }


            $country = Country::where('iso3', $data['birth_country_code'])->first();
            if (!$country) {
                return response()->json(['error' => 'Impossible de trouver ce pays']);
            }

            $request->merge([
                'origin_country_id' => $country['id'],
                'email' => $data['email'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'telephone' => $data['phone_number_indicatif'] . $data['phone_number'],
                'birthdate' => $data['birthdate'],
                'birth_place' => $data['birth_place'],
                'npi' => $data['npi'],
                'address' => $data['address'],
            ]);

            $identity = $this->identityRepository->create($request->only([
                'country_id',
                'email',
                'firstname',
                'lastname',
                'telephone',
                'birthdate',
                'birth_place',
                'address',
                'sex',
                'npi',
            ]));

            $request->merge([
                'identity_id' => $identity->id,
                'bfu' => $data['bfu']
            ]);

            $owner = $this->ownerRepository->store($request->only([
                'bfu',
                'identity_id',
            ]));

            DB::commit();
            return $owner->load($owner::relations())->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function storeByIfu($data):Model
    {
        DB::beginTransaction();
        try {

            $companyData = Institution::where('ifu', $data['ifu'])->first();
            if (!$companyData) {
                if (app()->environment('local') || app()->environment('dev') || in_array($data['ifu'], Utils::LOCAL_IFU)) {
                    $companyData = Http::get(config('app.sandbox_host') . '/companies/' . $data['ifu'])->json();
                } else {
                    $companyData = (new DGIService)->getCompanyByIFU($data['ifu']);
                }

                if (!isset($companyData['object']['ifu'])) {
                    return response()->json(['error' => 'Institution introuvable'], ResponseAlias::HTTP_NOT_FOUND);
                }

                $institutionData = [
                    'ifu' => $companyData['object']['ifu'],
                    'name' => $companyData['object']['raisonSociale'],
                    'email' => $companyData['object']['email'],
                    'telephone' => $companyData['object']['telephone'],
                    'type_id' => $data['type_id']?? InstitutionType::where('name', InstitutionTypesEnum::company->name)->pluck('id')->first(),
                    'town_id' => $data['town_id']?? null,
                    'logo' => $data['logo']?? null
                ];
                $companyData = (new InstitutionRepository)->store($institutionData);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
        return $companyData;
    }

    public function subscribeOwner($data)
    {
        DB::beginTransaction();
        try {
            $data['password'] = Hash::make($data['password']);
            $data['username'] = $data['email'];
            $user = $this->userService->create($data, true);

            $data['identity_id'] = $user->identity_id;
            $this->ownerRepository->store($data);

            DB::commit();
            return $user;
        } catch (\Exception $exception) {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
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
}
