<?php

namespace App\Models;

use App\Models\Config\OwnerType;
use App\Models\Config\Service;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleCharacteristic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePriceVariation extends Model
{
    use HasFactory;

    public function service()
    {
        return $this->belongsTo(Service::class,'type_id');
    }

    public function ownerType()
    {
        return $this->belongsTo(OwnerType::class,'type_id');
    }

    public function vehicleCharacteristic()
    {
        return $this->belongsTo(VehicleCharacteristic::class,'type_id');
    }

    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class,'type_id');
    }
}
