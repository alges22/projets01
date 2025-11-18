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
use Spatie\Activitylog\LogOptions;

class GeographicalArea extends Model implements CanFilterContract
{
    use HasFactory, Filterable, HasUuids, SecureDelete, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'code',
        'authorized_registration_format',
        'validation_criteria',
        'restrictions_or_special_requirements',
        'staff_ids'
    ];

    protected $casts = [
        'staff_ids' => 'array'
    ];

    const CITY = 'city';
    const REGION = 'region';
    const COUNTRY = 'country';

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
        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%")
                ->orWhere('type', 'like', "%$keyword%")
                ->orWhere('code', 'like', "%$keyword%")
            ;
        });
    }

    private function getEntityName() : string
    {
        return "Zone géographique";
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
}
