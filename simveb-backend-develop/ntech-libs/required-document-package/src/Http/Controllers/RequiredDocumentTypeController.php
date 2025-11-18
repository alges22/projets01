<?php

namespace Ntech\RequiredDocumentPackage\Http\Controllers;

use App\Http\Controllers\Controller;
use Ntech\RequiredDocumentPackage\Http\Requests\RequiredDocumentTypeRequest;
use Ntech\RequiredDocumentPackage\Models\RequiredDocumentType;
use Ntech\RequiredDocumentPackage\Repositories\DocumentTypeRepository;
use Ntech\RequiredDocumentPackage\Repositories\RequiredDocumentTypeRepository;

class RequiredDocumentTypeController extends Controller
{
    public function __construct( private readonly RequiredDocumentTypeRepository $requiredDocumentTypeRepository)
    {
        // $this->authorizeResource(RequiredDocumentType::class);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->requiredDocumentTypeRepository->getAll());
    }

    /**
     * @return Response|ResponseFactory
     */
    public function create()
    {
        return response([
            'relation_types' => $this->requiredDocumentTypeRepository->getRelationTypes(),
            'document_types' => (new DocumentTypeRepository)->getAll(false),
        ]);
    }

    /**
     * @param RequiredDocumentTypeRequest $request
     * @return Response|ResponseFactory
     */
    public function store(RequiredDocumentTypeRequest $request)
    {
        return response($this->requiredDocumentTypeRepository->store($request->validated()));
    }

    /**
     * @param RequiredDocumentType $requiredDocumentType
     * @return Response|ResponseFactory
     */
    public function show(RequiredDocumentType $requiredDocumentType)
    {
        return response($requiredDocumentType->load(RequiredDocumentType::relations()));
    }

    /**
     * @param RequiredDocumentType $requiredDocumentType
     * @param RequiredDocumentTypeRequest $request
     * @return Response|ResponseFactory
     */
    public function update(RequiredDocumentType $requiredDocumentType, RequiredDocumentTypeRequest $request)
    {
        return response($this->requiredDocumentTypeRepository->update($requiredDocumentType, $request->validated()));
    }

    /**
     * @param RequiredDocumentType $requiredDocumentType
     * @return Response|ResponseFactory
     */
    public function destroy(RequiredDocumentType $requiredDocumentType)
    {
        return response($this->requiredDocumentTypeRepository->delete($requiredDocumentType));
    }
}
