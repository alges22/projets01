<?php

namespace App\Models\Config;

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
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class OwnerType extends Model implements CanFilterContract
{
    use HasFactory,
    HasUuids,
    ActivityTrait,
    LogsActivity,
    SecureDelete,
    Filterable,
    SoftDeletes;

    protected $fillable = [
        'name',
        'label',
        'price'
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

    private function getEntityName() : string
    {
        return 'Type de propriétaire';
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

    public function managementCenters()
    {
        return $this->belongsToMany(ManagementCenter::class);
    }

    public function services(): MorphToMany
    {
        return $this->morphToMany(Service::class, 'model', 'service_price_variations');
    }

}
