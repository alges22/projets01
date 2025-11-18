<?php

namespace App\Models\Vehicle;

use App\Models\Config\Park;
use App\Models\Config\Service;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Database\Factories\Vehicle\VehicleCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleCategory extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'name',
        'label',
        'nb_plate',
        'price'
    ];

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
            new RelativeFilter('label'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('label', 'like', "%$keyword%");
        });
    }

    private function getEntityName(): string
    {
        return "Catégorie de véhicule";
    }

    static function relations()
    {
        return [
            //
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            //
        ];
    }

    public function immatriculationFormat()
    {
        return $this->hasOne(ImmatriculationFormat::class);
    }

    public function parks()
    {
        return $this->belongsToMany(Park::class);
    }

    public function services(): MorphToMany
    {
        return $this->morphToMany(Service::class, 'model', 'service_price_variations');
    }

    /**
     * Get all of the vehicles for the VehicleCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'vehicle_category_id');
    }

    public static function newFactory(): Factory
    {
        return VehicleCategoryFactory::new();
    }
}
