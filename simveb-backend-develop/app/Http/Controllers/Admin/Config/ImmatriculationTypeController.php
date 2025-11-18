<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImmatriculationTypeRequest;
use App\Models\Config\ImmatriculationType;
use App\Repositories\ImmatriculationTypeRepository;
use Illuminate\Http\Request;

class ImmatriculationTypeController extends Controller
{
    public function __construct(private readonly ImmatriculationTypeRepository $repository)
    {
        $this->authorizeResource(ImmatriculationType::class);
    }

    /**
     *
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     *
     */
    public function create()
    {
        return response($this->repository->create());
    }

    /**
     *
     */
    public function store(ImmatriculationTypeRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     *
     */
    public function show(ImmatriculationType $immatriculationType)
    {
        return response($immatriculationType->load($immatriculationType::relations()));
    }

    /**
     *
     */
    public function update(ImmatriculationTypeRequest $request, ImmatriculationType $immatriculationType)
    {
        return response($this->repository->update($immatriculationType, $request->validated()));
    }

    /**
     *
     */
    public function destroy(ImmatriculationType $immatriculationType)
    {
        return response($this->repository->destroy($immatriculationType));
    }
}
