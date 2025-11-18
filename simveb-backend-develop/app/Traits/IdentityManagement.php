<?php


namespace App\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Ntech\UserPackage\Models\Identity;
use Ntech\UserPackage\Services\UserService;

trait IdentityManagement
{
    protected function createIdentity($request): Model|Builder
    {
        return $identity = Identity::query()->create($request->only(
            ['firstname', 'lastname', 'sexe', 'telephone', 'email', 'ville', 'other_attributes','momo_number','account_number','ifu','birthday']
        ));
    }

    protected function updateIdentity($request, $identity)
    {
        $userService = new UserService;

             if($request->has('email'))
             {
                 $userService->updateUserByIdentity($identity->id,["username"=>$request->telephone]);
             }

        $identity->update($request->only(
                    ['firstname', 'lastname', 'sexe', 'telephone', 'email', 'other_attributes','image']
                ));

        return $identity->refresh();
    }

    protected function getIdentityById($identityId): Model|Collection|Builder|array|null
    {
        return Identity::query()->find($identityId);
    }
}
