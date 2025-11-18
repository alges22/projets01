<?php

namespace App\Services\Immatriculation;

use App\Models\Immatriculation\FormatComponent;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Repositories\Crud\CrudRepository;
use App\Repositories\Immatriculation\ImmatriculationFormatRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response as ResponseAlias;

class ImmatriculationFormatService
{

    private CrudRepository $formatComponentRepository;
    public ImmatriculationFormatRepository $repository;

    public function __construct()
    {
        $this->repository = new ImmatriculationFormatRepository;
        $this->formatComponentRepository = new CrudRepository(FormatComponent::class);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store($data)
    {
        DB::beginTransaction();
        try {
            $format =  $this->repository->store(
                [
                    'format' => $this->generateFormatArray($data['components']),
                    'vehicle_category_id' => $data['vehicle_category_id'] ?? null,
                    'profile_type_id' => $data['profile_type_id'] ?? null,
                ]
            );

            foreach ($data['components'] as $component) {
                $format->components()->attach([$component['id'] =>
                [
                    'value' => $component['value'] ?? null,
                    'position' => $component['position']
                ]]);
            }

            DB::commit();

            return $format->load('vehicleCategory:id,name');
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, app()->isProduction() ? 'Oups une erreur est survenue' : $e->getMessage());
        }
    }

    public function update(ImmatriculationFormat $format, $data)
    {
        DB::beginTransaction();
        try {
            $format = $this->repository->update(
                $format,
                [
                    'format' => $this->generateFormatArray($data['components']),
                    'vehicle_category_id' => $data['vehicle_category_id'] ?? null,
                    'profile_type_id' => $data['profile_type_id'] ?? null,
                ]
            );

            foreach ($data['components'] as $component) {
                $format->components()->syncWithoutDetaching([$component['id'] => [
                    'value' => $component['value'] ?? null,
                    'length' => $component['length'] ?? null,
                    'position' => $component['position']
                ]]);
            }
            DB::commit();

            return $format->load('vehicleCategory:id,name');
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, app()->isProduction() ? 'Oups une erreur est survenue' : $e->getMessage());
        }
    }

    public function generateFormatArray($components): array
    {
        $format = [];
        for ($i = 0; $i < count($components); $i++) {
            $selectedComponent = collect($components)->where('position', ($i + 1))->first();
            $component = $this->formatComponentRepository->find($selectedComponent['id']);
            $format[$i] = $component->code;
        }

        return $format;
    }
}
