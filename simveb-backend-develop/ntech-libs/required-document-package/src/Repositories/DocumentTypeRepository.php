<?php

namespace Ntech\RequiredDocumentPackage\Repositories;

use Ntech\RequiredDocumentPackage\Models\DocumentType;

class DocumentTypeRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = app(DocumentType::class);
    }

    /**
     * @param
     * @return mixed
     */
    public function getAll($paginate = true): mixed
    {
        $query = $this->model->newQuery()->orderByDesc('created_at')->filter();

        return $paginate ? $query->paginate(request('per_page', '15')) : $query->get();
    }

    /**
     * @return DocumentType
     */
    function store(array $data)
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * @param array $data
     * @param DocumentType model
     * @return DocumentType
     */
    function update(DocumentType $model, array $data): DocumentType
    {
        $model->update($data);

        return $model;
    }

    /**
     * @param DocumentType model
     * @return mixed
     */
    function delete(DocumentType $model)
    {
        $model->secureDelete($this->model->secureDeleteRelations());

        return $model;
    }
}
