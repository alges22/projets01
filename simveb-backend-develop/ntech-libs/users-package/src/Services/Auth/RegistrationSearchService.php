<?php

namespace Ntech\UserPackage\Services\Auth;

use App\Models\Config\District;
use App\Models\Config\State;
use App\Models\Config\Town;
use App\Models\Config\Village;

class RegistrationSearchService
{
    public function states()
    {
        return State::select(['id', 'code', 'name'])->get();
    }

    public function towns()
    {
        return Town::when(request('state_id'), function ($q) {
            $q->where('state_id', request('state_id'));
        })->select(['id', 'code', 'name'])->get();
    }

    public function districts()
    {
        return District::when(request('town_id'), function ($q) {
            $q->where('town_id', request('town_id'));
        })->select(['id', 'code', 'name'])->get();
    }

    public function villages()
    {
        return Village::when(request('district_id'), function ($q) {
            $q->where('district_id', request('district_id'));
        })->select(['id', 'code', 'name'])->get();
    }
}
