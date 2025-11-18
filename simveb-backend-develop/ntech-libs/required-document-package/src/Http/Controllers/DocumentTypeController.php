<?php

namespace Ntech\RequiredDocumentPackage\Http\Controllers;

use App\Http\Controllers\Controller;
use Ntech\RequiredDocumentPackage\Http\Requests\DocumentTypeRequest;
use Ntech\RequiredDocumentPackage\Models\DocumentType;
use Ntech\RequiredDocumentPackage\Repositories\DocumentTypeRepository;

class DocumentTypeController extends Controller
{
    public function __construct( private readonly DocumentTypeRepository $documentTypeRepository)
    {
        // use this after adding permissions
        // $this->authorizeResource(DocumentType::class);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->documentTypeRepository->getAll());
    }

    /**
     * @return Response|ResponseFactory
     */
    public function show(DocumentType $documentType)
    {
        return response($documentType);
    }

    /**
     * @param DocumentTypeRequest $request
     * @return Response|ResponseFactory
     */
    public function store(DocumentTypeRequest $request)
    {
        return response($this->documentTypeRepository->store($request->validated()));
    }

    /**
     * @param DocumentTypeRequest $request
     * @param DocumentType $documentType
     * @return Response|ResponseFactory
     */
    public function update(DocumentTypeRequest $request, DocumentType $documentType)
    {
        return response($this->documentTypeRepository->update($documentType,$request->validated()));
    }

    /**
     * @param DocumentType $documentType
     * @return Response|ResponseFactory
     */
    public function destroy(DocumentType $documentType)
    {
        return response($this->documentTypeRepository->delete($documentType));
    }
}
