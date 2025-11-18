<?php

namespace App\Models;

use App\Models\Config\TransformationType;
use App\Models\Order\Demand;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\HasStatusLabel;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;

class VehicleTransformation extends Model
{
    use HasFactory, HasUuids, Filterable, ActivityTrait, HasStatusLabel;

    protected $fillable = [
        'demand_id',
        'vehicle_id',
        'owner_id',
        'status',
    ];

    static function relations(): array
    {
        return [
            //
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
            //
        ];
    }

    public function getEntityName() : string
    {
        return "Transformation de véhicule";
    }

    public function demand(): BelongsTo
    {
        return $this->belongsTo(Demand::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    public function transformationCharacteristics(): HasMany
    {
        return $this->hasMany(TransformationCharacteristic::class, 'transformation_id');
    }

}
