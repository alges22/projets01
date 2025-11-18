<?php

namespace App\Models\Vehicle;

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
use Spatie\Activitylog\LogOptions;

class VehiclePower extends Model implements CanFilterContract
{
    use HasFactory, Filterable, HasUuids, SecureDelete, SoftDeletes;

    protected $fillable = ['unit', 'min_value', 'max_value'];

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
        if (is_numeric($keyword)) {
            return $query->where(function (Builder $query) use ($keyword) {
                $query->where('min_value', '>=', $keyword);
            });
        }

        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('unit', 'like', "%$keyword%");
        });
    }

    private function getEntityName(): string
    {
        return "Puissance de véhicule";
    }

    public static function relations()
    {
        return [];
    }

    public static function secureDeleteRelations()
    {
        return [];
    }
}
