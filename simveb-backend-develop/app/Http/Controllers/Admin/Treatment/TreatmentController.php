<?php

namespace App\Http\Controllers\Admin\Treatment;

use App\Http\Controllers\Controller;
use App\Services\Treatment\TreatmentService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class TreatmentController extends Controller
{
    public function __construct(private TreatmentService $service)
    {
    }

    public function create(): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        return response($this->service->create());
    }
}
