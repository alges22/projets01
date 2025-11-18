<?php

namespace App\Repositories;

use App\Models\Config\OwnerType;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OwnerTypeRepository
{
    /**
     * @param array $data
     * @return OwnerType $ownerType
     */
    public function store(array $data)
    {
        try {
            DB::beginTransaction();

            $data['name'] = Str::slug($data['label'], '_');

            $ownerType = OwnerType::create($data);

            DB::commit();
            return $ownerType->load(OwnerType::relations());
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * @param OwnerType $ownerType
     * @param array $data
     * @return OwnerType $ownerType
     */
    public function update(OwnerType $ownerType, array $data)
    {
        try {
            DB::beginTransaction();

            $data['name'] = Str::slug($data['label'], '_');

            $ownerType->update($data);

            DB::commit();
            return $ownerType->load(OwnerType::relations());
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }
}
