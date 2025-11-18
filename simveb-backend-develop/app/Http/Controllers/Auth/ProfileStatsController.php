<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\Auth\ProfileRepository;
use Illuminate\Http\Request;

class ProfileStatsController extends Controller
{
    public function __construct(private readonly ProfileRepository $repository)
    {
        //
    }

    /**
     *
     */
    public function totalByTypes()
    {
        return response($this->repository->getTotalProfilesByTypes());
    }
}
