<?php

namespace Ntech\RequiredDocumentPackage\Traits;

use Ntech\RequiredDocumentPackage\Models\RequiredDocumentType;

trait HasRequiredDocumentTypes
{
    public static function requiredDocumentTypes()
    {
        return RequiredDocumentType::with(RequiredDocumentType::relations())
            ->where('relation_type', (new static)::class)
            ->select(['id', 'relation_type', 'document_type_id', 'required'])
            ->get();
    }
    public static function documents()
    {
        return RequiredDocumentType::with(RequiredDocumentType::relations())
            ->where('relation_type', (new static)::class)
            ->select(['id', 'relation_type', 'document_type_id', 'required'])
            ->get();
    }
}
