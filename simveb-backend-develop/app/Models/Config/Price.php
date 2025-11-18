<?php

namespace App\Models\Config;

use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleType;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Price extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        Filterable,
        SoftDeletes;

    protected $fillable = [
        'characteristic_id',
        'service_id',
        'owner_type_id',
        'vehicle_type_id',
        'vehicle_category_id',
        'price',
    ];

    protected $casts = [];

    protected $guarded=[];


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

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query
                ->whereRelation('service', 'name','like', "%$keyword%")
                ->whereRelation('service', 'name','like', "%$keyword%");
        });
    }

    private function getEntityName() : string
    {
        return 'Prix';
    }

    public static function relations()
    {
        return [
            'service',
            'ownerType',
            'vehicleType',
            'characteristic',
        ];
    }

    public static function secureDeleteRelations()
    {
        return [

        ];
    }


    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function ownerType()
    {
        return $this->belongsTo(OwnerType::class);
    }

    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class,'vehicle_category_id');
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class,'vehicle_type_id');
    }

    public function characteristic()
    {
        return $this->belongsTo(VehicleCharacteristic::class,'characteristic_id');
    }
}
