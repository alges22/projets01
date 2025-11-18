<?php

namespace App\Repositories;

use App\Models\Config\ImmatriculationType;
use App\Models\Plate\PlateColor;
use App\Repositories\Crud\AbstractCrudRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ImmatriculationTypeRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(ImmatriculationType::class);
    }

    /**
     * @return array
     */
    public function create()
    {
        return [
            'plate_colors' => PlateColor::select(['id', 'label'])->get(),
        ];
    }

    /**
     *
     */
    public function store(array $data, $request = null): ?Model
    {
        DB::beginTransaction();

        try {
            $immatriculationType = parent::store($data);
            $immatriculationType->plateColors()->attach($data['plate_colors']);
            DB::commit();

            return $immatriculationType->load($immatriculationType::relations());
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     *
     */
    public function update(Model $model, array $data, $request = null): Model
    {
        DB::beginTransaction();

        try {
            $immatriculationType = parent::update($model, $data);
            $immatriculationType->plateColors()->sync($data['plate_colors']);
            DB::commit();

            return $immatriculationType->load($immatriculationType::relations());
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }
}
