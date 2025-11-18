<?php

namespace App\Models\Vehicle;

use App\Models\Config\TransformationType;
use App\Models\Order\Demand;
use App\Models\TransformationCharacteristic;
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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VehicleCharacteristicCategory extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'name',
        'label',
        'type',
        'field_name',
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
            new RelativeFilter('type'),
            new RelativeFilter('field_name'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('label', 'like', "%$keyword%");
        });
    }

    private function getEntityName() : string
    {
        return 'Catégorie de caractéristique de véhicule';
    }

    static function relations()
    {
        return [
            'vehicleCharacteristics',
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            'vehicleCharacteristics',
        ];
    }

    public function vehicleCharacteristics(): HasMany
    {
        return $this->hasMany(VehicleCharacteristic::class, 'category_id');
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(TransformationType::class, 'transformation_type_vehicle_characteristic_category','category_id', 'type_id')->withTimestamps();
    }
}
