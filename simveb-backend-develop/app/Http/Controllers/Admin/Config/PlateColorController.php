<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Response;
use App\Http\Controllers\ResponseFactory;
use App\Http\Requests\PlateColorRequest;
use App\Models\Plate\PlateColor;
use App\Repositories\PlateColorRepository;
use App\Traits\CrudRepositoryTrait;

class PlateColorController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly PlateColorRepository $plateColorRepository)
    {
        $this->initRepository(PlateColor::class);
        $this->authorizeResource(PlateColor::class);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * @param PlateColorRequest $request
     * @return Response|ResponseFactory
     */
    public function store(PlateColorRequest $request)
    {
        return response($this->plateColorRepository->store($request->validated()));
    }

    /**
     * @param PlateColor $plateColor
     * @return Response|ResponseFactory
     */
    public function show(PlateColor $plateColor)
    {
        return response($plateColor);
    }

    /**
     * @param PlateColorRequest $request
     * @param PlateColor $plateColor
     * @return Response|ResponseFactory
     */
    public function update(PlateColorRequest $request, PlateColor $plateColor)
    {
        return response($this->plateColorRepository->update($plateColor, $request->validated()));
    }

    /**
     * @param PlateColor $plateColor
     * @return Response|ResponseFactory
     */
    public function destroy(PlateColor $plateColor)
    {
        return response($this->repository->destroy($plateColor));
    }
}
