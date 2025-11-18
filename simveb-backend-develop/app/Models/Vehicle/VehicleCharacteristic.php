<?php

namespace App\Models\Vehicle;

use App\Models\Config\Service;
use App\Models\Order\Demand;
use App\Models\TransformationCharacteristic;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
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

class VehicleCharacteristic extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'category_id',
        'value',
        'min_value',
        'max_value',
        'cost',
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
            new RelativeFilter('value'),
            new ExactFilter('category_id'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query) use ($keyword) {
            $query->whereHas('category', function ($q) use ($keyword) {
                $q->where('label', 'like', "%$keyword%");
            });
        });
    }

    private function getEntityName(): string
    {
        return 'Caractéristique de véhicule';
    }

    public function category()
    {
        return $this->belongsTo(VehicleCharacteristicCategory::class, 'category_id');
    }

    static function relations()
    {
        return [
            'category'
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            //
        ];
    }
    public function services(): MorphToMany
    {
        return $this->morphToMany(Service::class, 'model', 'service_price_variations');
    }

    public function transformationCharacteristics(): HasMany
    {
        return $this->hasMany(TransformationCharacteristic::class);
    }

    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }
}
