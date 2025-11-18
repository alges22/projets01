<?php

namespace Ntech\RequiredDocumentPackage\Http\Policies;

use App\Models\Account\User;
use Ntech\RequiredDocumentPackage\Models\RequiredDocumentType;

class RequiredDocumentTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->hasPermissionTo('browse-required-document-type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RequiredDocumentType $requiredDocumentType): bool
    {
        return  $user->hasPermissionTo('browse-required-document-type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->hasPermissionTo('store-required-document-type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RequiredDocumentType $requiredDocumentType): bool
    {
        return  $user->hasPermissionTo('update-required-document-type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RequiredDocumentType $requiredDocumentType): bool
    {
        return  $user->hasPermissionTo('delete-required-document-type');

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RequiredDocumentType $requiredDocumentType): bool
    {
        return  $user->hasPermissionTo('restore-document-type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RequiredDocumentType $requiredDocumentType): bool
    {
        return  $user->hasPermissionTo('force-delete-required-document-type');
    }
}
