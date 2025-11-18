<?php

namespace App\Repositories;

use App\Models\Config\Service;

class PortalRepository
{

    public function services()
    {
        return Service::whereNull('parent_service_id')->with(['children'])->get();
    }
}
