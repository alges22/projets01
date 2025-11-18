<?php

namespace App\Models;

use App\Interfaces\ModelHasRelations;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OwnerHistory extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
    HasUuids,
    Filterable,
    ActivityTrait,
    LogsActivity,
    SecureDelete,
    SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'vehicle_owner_id',
    ];

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
        ];
    }

    static function relations(): array
    {
        return [
            'vehicle',
            'vehicleOwner'
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [

        ];
    }

    private function getEntityName() : string
    {
        return "Historique des propriétaires d'un véhicule";
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleOwner()
    {
        return $this->belongsTo(VehicleOwner::class);
    }
}
