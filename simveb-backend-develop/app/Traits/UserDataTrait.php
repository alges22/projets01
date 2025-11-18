<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

trait UserDataTrait
{

    protected function user()
    {
        if (Auth::check()){
            return Auth::user()->load('identity.staff');
        }else{
            throw new UnauthorizedException();
        }
    }

    public function staff()
    {
        return $this->user()->identity->staff;
    }
}
