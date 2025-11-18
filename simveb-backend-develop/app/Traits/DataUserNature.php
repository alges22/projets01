<?php

namespace App\Traits;

use App\Exceptions\RetrieveDataUserNatureException;
use Illuminate\Support\Facades\Auth;

/**
 * Trait DataUserNature
 * @package Suntech\MainService\Traits
 */
trait DataUserNature
{
    protected $nature;
    protected $user;
    protected $staff;
    protected $client;

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @throws RetrieveDataUserNatureException
     */
    protected function user()
    {
        $message = "Unable to find the user";

        try {
            $this->user = Auth::user();

        } catch (\Exception $exception) {
            throw new RetrieveDataUserNatureException($message);
        }

        if (is_null($this->user)) {
            throw new RetrieveDataUserNatureException($message);
        }

        return $this->user;
    }

    /**
     * @return mixed
     * @throws RetrieveDataUserNatureException
     */
    protected function staff()
    {

        $message = "Unable to find the user staff";

        try {
            $this->staff = $this->user()->load('identity.staff.projects')->identity->staff;
        } catch (\Exception $exception) {
            throw new RetrieveDataUserNatureException($exception->getMessage());
        }

        if (is_null($this->staff)) {
            throw new RetrieveDataUserNatureException($message);
        }

        return $this->staff;
    }


}
