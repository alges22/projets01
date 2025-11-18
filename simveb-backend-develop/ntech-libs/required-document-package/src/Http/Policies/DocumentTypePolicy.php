<?php

namespace Ntech\RequiredDocumentPackage\Http\Policies;

use App\Models\Account\User;
use Ntech\RequiredDocumentPackage\Models\DocumentType;

class DocumentTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->hasPermissionTo('browse-document-type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentType $documentType): bool
    {
        return  $user->hasPermissionTo('browse-document-type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->hasPermissionTo('store-document-type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentType $documentType): bool
    {
        return  $user->hasPermissionTo('update-document-type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentType $documentType): bool
    {
        return  $user->hasPermissionTo('delete-document-type');

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DocumentType $documentType): bool
    {
        return  $user->hasPermissionTo('restore-document-type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DocumentType $documentType): bool
    {
        return  $user->hasPermissionTo('force-delete-document-type');
    }
}
