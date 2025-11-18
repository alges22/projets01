<?php

namespace App\Interfaces;

use App\Models\Order\Demand;
use Illuminate\Database\Eloquent\Model;

interface DemandServiceAdapter
{
    public function initDemand(Demand $demand, array $data) :  Model|Demand;

    public function submit(Demand $demand) : Model|Demand;
    public function validate(Demand $demand) :  Model|Demand;
}
