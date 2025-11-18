<?php

namespace Ntech\RequiredDocumentPackage\Repositories;

use Ntech\RequiredDocumentPackage\Models\RequiredDocumentType;

class RequiredDocumentTypeRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = app(RequiredDocumentType::class);
    }

    /**
     * @param bool $paginate
     * @return mixed
     */
    public function getAll($paginate = true): mixed
    {
        $query = $this->model->newQuery()->filter()->orderByDesc('required_document_types.created_at')->with($this->model::relations());

        return $paginate ? $query->paginate(request('per_page','15')) : $query->get();
    }

    /**
     * @return array
     */
    public function getRelationTypes(): array
    {
        $formatedData = [];
        foreach (RequiredDocumentType::types() as $relation) {
            $formatedData[] = [
                'classname' => $relation,
                'basename' => __('RequiredDocumentPackage::model.' . $this->getClassBasename($relation)),
            ];
        }

        return $formatedData;
    }

    /**
     * @param array $data
     * @return RequiredDocumentType
     */
    public function store(array $data): RequiredDocumentType
    {
        $requiredDocumentType = $this->model->newQuery()->create($data);

        return $requiredDocumentType;
    }

    /**
     * @param RequiredDocumentType $requiredDocumentType
     * @param array $data
     * @return RequiredDocumentType
     */
    public function update(RequiredDocumentType $requiredDocumentType, array $data): RequiredDocumentType
    {
        $requiredDocumentType->update($data);

        return $requiredDocumentType;
    }

    /**
     * @param RequiredDocumentType $requiredDocumentType
     * @return mixed
     */
    public function delete(RequiredDocumentType $requiredDocumentType): mixed
    {
        $requiredDocumentType->secureDelete($this->model->secureDeleteRelations());

        return $requiredDocumentType;
    }

    /**
     * @param string $class
     * @return string
     */
    public function getClassBasename(string $class): string
    {
        $class = explode('\\', $class);
        return end($class);
    }
}
