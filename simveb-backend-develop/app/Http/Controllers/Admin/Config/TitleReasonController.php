<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleReasonRequest;
use App\Models\Config\TitleReason;
use App\Repositories\TitleReasonRepository;
use App\Traits\CrudRepositoryTrait;

class TitleReasonController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly TitleReasonRepository $titleReasonRepository)
    {
        $this->initRepository(TitleReason::class);
        $this->authorizeResource(TitleReason::class);

    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll(true, TitleReason::relations()));
    }

    /**
     * @param TitleReasonRequest $request
     * @return Response|ResponseFactory
     */
    public function store(TitleReasonRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response($this->titleReasonRepository->create());
    }

    /**
     * @param TitleReason $titleReason
     * @return Response|ResponseFactory
     */
    public function show(TitleReason $titleReason)
    {
        return response($titleReason->load(TitleReason::relations()));
    }

    /**
     * @param TitleReasonRequest $request
     * @param TitleReason $titleReason
     * @return Response|ResponseFactory
     */
    public function update(TitleReasonRequest $request, TitleReason $titleReason)
    {
        return response($this->repository->update($titleReason, $request->validated()));
    }

    /**
     * @param TitleReason $titleReason
     * @return Response|ResponseFactory
     */
    public function destroy(TitleReason $titleReason)
    {
        return response($this->repository->destroy($titleReason));
    }
}
