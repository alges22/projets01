<?php

namespace Ntech\MetadataPackage\Http\Controllers\Metadata;

use Ntech\MetadataPackage\Enums\MetaDataKeys;
use Ntech\MetadataPackage\Http\Requests\MetaDataRequest;
use Ntech\MetadataPackage\Repositories\MetaDataRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class MetaDataController extends Controller
{
    public function __construct(private readonly MetaDataRepository $metaDataRepository)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return response($this->metaDataRepository->getList());
    }

    public function show($name){

        return response($this->metaDataRepository->getMetaDataByName($name));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MetaDataRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function update(MetaDataRequest $request)
    {
        return response($this->metaDataRepository->update($request->metadata));
    }


}
