<?php

namespace App\Http\Controllers;

use App\Repositories\Plate\PlateRepository;
use Illuminate\Http\Request;

class PlateStatsController extends Controller
{
    public function __construct(private readonly PlateRepository $repository)
    {
        //
    }

    /**
     *
     */
    public function total()
    {
        return response($this->repository->getTotalPlates());
    }
}
