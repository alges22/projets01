<?php

namespace App\Models\Config;

use App\Models\Space\Space;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleType;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
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

class Park extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'address',
        'longitude',
        'latitude',
        'space_id',
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
            new RelativeFilter('name'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('name', 'like', "%$keyword%");
        });
    }

    private function getEntityName() : string
    {
        return 'Parc';
    }

    public static function relations()
    {
        return [
           'space', 'vehicleTypes', 'vehicleCategories', 'towns'
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            //
        ];
    }

    public function vehicleCategories()
    {
        return $this->belongsToMany(VehicleCategory::class);
    }

    public function vehicleTypes()
    {
        return $this->belongsToMany(VehicleType::class);
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function towns()
    {
        return $this->belongsToMany(Town::class);
    }

    public function managementCenters()
    {
        return $this->belongsToMany(ManagementCenter::class);
    }
}
