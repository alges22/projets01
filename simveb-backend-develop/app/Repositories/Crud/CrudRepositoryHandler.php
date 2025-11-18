<?php

namespace App\Repositories\Crud;

use App\Traits\UploadFile;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Facades\CauserResolver;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CrudRepositoryHandler
{

    use UploadFile;

    /**
     * @var \Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    public Model $model;

    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->model = app($class);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFail($id): mixed
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * @param $id
     * @return Model|null
     */
    public function find($id): Model | null
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param array $conditions
     * @return Model|null
     */
    public function findWhere(array $conditions): Model | null
    {
        return $this->model->newQuery()->where($conditions)->first();
    }

    /**
     * @param bool $paginate
     * return list of model
     * @return mixed
     */
    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        $query = $this->model
            ->newQuery()
            ->with($relations)
            ->orderByDesc('created_at')
            ->filter();

        return $paginate
            ? $query->paginate(request('per_page', '15'))
            : $query->get();
    }

    /**
     * @param array $data
     * @param $request
     * @return Model|null
     */
    public function store(array $data, $request = null): Model | null
    {
        DB::beginTransaction();

        try {
            if ($request?->hasFile("image") && $filePath = $this->saveFile($request, "images", "image")) {
                $data['image'] = $filePath;
            }
            CauserResolver::setCauser(getOnlineProfile());

            $model = $this->model->newQuery()->create($data);

            if ($request?->has('images')) {
                if ($imagePaths = $this->saveMultipleFiles($request, $this->model->getTable(), "images")) {
                    foreach ($imagePaths as $imagePath) {
                        $this->createFile([
                            "path" => $imagePath,
                            "model_type" => $this->model::class,
                            "model_id" => $model->id
                        ]);
                    }
                }
            }

            DB::commit();
            return $model;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * @param $data
     * @return Model|Builder
     */
    public function storeOrUpdate(array $data, array $constraints = null, $serviceExists = false): Model|Builder
    {
        CauserResolver::setCauser(getOnlineProfile());

        if ($constraints) {
            return $serviceExists
                ? $this->model::query()->create($constraints)
                : $this->model::query()->updateOrCreate($constraints, $data);
        }

        return $this->model::query()->updateOrCreate($data);
    }

    /**
     * @param Model $model
     * @param array $data
     * @param $request
     * @return Model
     */
    function update(Model $model, array $data, $request = null): Model
    {
        DB::beginTransaction();
        try {
            if ($request?->hasFile("image")) {
                $filePath = $this->saveFile($request, "images", "image");
                $data['image'] = $filePath;
            }
            CauserResolver::setCauser(getOnlineProfile());

            $model->update($data);

            if ($request?->has('images')) {
                $imagePaths = $this->saveMultipleFiles($request, $this->model->getTable(), "images");
                if ($imagePaths) {
                    foreach ($imagePaths as $imagePath) {
                        $this->createFile([
                            "path" => $imagePath,
                            "model_type" => $this->model::class,
                            "model_id" => $model->id
                        ]);
                    }
                }
            }

            DB::commit();
            return $model->refresh();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * @param Model $model
     * @return Model
     */
    function destroy(Model $model): Model
    {
        CauserResolver::setCauser(getOnlineProfile());

        $model->secureDelete($this->model::secureDeleteRelations());

        return $model;
    }
}
