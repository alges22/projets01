<?php

namespace App\Models\Config;

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

class State extends Model implements CanFilterContract
{
    use HasFactory,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        Filterable,
        HasUuids,
        SoftDeletes;


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
            $query->whereRaw('LOWER(code) LIKE ?',  ["%$keyword%"])
                ->orWhereRaw('LOWER(name) LIKE ?',  ["%$keyword%"]);
        });
    }

    private function getEntityName() : string
    {
        return 'Département';
    }


    public static function relations()
    {
        return [
        ];
    }

    public static function secureDeleteRelations()
    {
        return [
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function managementCenters()
    {
        return $this->hasMany(ManagementCenter::class);
    }

    public function towns()
    {
        return $this->hasMany(Town::class);
    }
}
