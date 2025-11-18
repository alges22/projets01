<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;

class ServiceStatsController extends Controller
{
    public function __construct(private readonly ServiceRepository $repository)
    {
        //
    }

    /**
     *
     */
    public function popular()
    {
        return response($this->repository->getMostPopulars());
    }

    /**
     *
     */
    public function unpopular()
    {
        return response($this->repository->getUnpopulars());
    }
}
