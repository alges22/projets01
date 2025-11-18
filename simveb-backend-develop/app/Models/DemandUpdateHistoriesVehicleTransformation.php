<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandUpdateHistoriesVehicleTransformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_transformation_id',
        'demand_update_history_id',
    ];
}
