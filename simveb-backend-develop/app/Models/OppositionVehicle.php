<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OppositionVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'opposition_id',
        'vehicle_id',
    ];
}
