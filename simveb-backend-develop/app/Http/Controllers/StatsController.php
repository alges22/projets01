<?php

namespace App\Http\Controllers;

use App\Repositories\StatsRepository;

class StatsController extends Controller
{
    public function __construct(private readonly StatsRepository $statsRepository)
    {
    }

    public function index()
    {
        return response($this->statsRepository->getAllStats());
    }

    public function immatriculationDemand()
    {
        return response($this->statsRepository->immatriculationDemand());
    }

    public function duplicateDemand()
    {
        return response($this->statsRepository->duplicateDemand());
    }

    public function stats()
    {
        return response($this->statsRepository->stats());
    }
}
