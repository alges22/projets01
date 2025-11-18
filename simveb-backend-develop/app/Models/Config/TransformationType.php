<?php

namespace App\Models\Config;

use App\Models\TransformationCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Models\VehicleTransformation;
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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TransformationType extends Model implements CanFilterContract
{
    use HasUuids, HasFactory, ActivityTrait, LogsActivity, Filterable, SecureDelete, SoftDeletes;

    protected $fillable = [
        'label',
        'description',
    ];

    /**
     * @return array
     */
    public static function relations(): array
    {
        return [
            'categoryCharacteristics:id,name,code,label,field_name',
        ];
    }

    /**
     * @return array
     */
    public static function secureDeleteRelations()
    {
        return [
            //
        ];
    }

    /**
     * @return string
     */
    private function getEntityName(): string
    {
        return "Type de transformation de véhicule";
    }

    /**
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('label'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        });
    }

    public function categoryCharacteristics():BelongsToMany
    {
        return $this->belongsToMany(VehicleCharacteristicCategory::class, 'transformation_type_vehicle_characteristic_category', 'type_id','category_id')->withTimestamps();
    }

    public function vehicleTransformations(): HasMany
    {
        return $this->hasMany(VehicleTransformation::class);
    }
}
