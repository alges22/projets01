<?php

namespace App\Http\Actions;

use Illuminate\Http\Request;
use App\Models\Config\Service;
use App\Repositories\ServiceRepository;

class ToggleServiceAction
{

    public function __invoke(Service $service,Request $request,ServiceRepository $repository)
    {
        return response($repository->toggleService($service, $request));
    }
}
