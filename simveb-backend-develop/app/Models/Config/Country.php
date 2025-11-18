<?php

namespace App\Models\Config;

use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Country extends Model implements CanFilterContract
{
    use HasFactory,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        Filterable;


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
            $query->where('code', 'like', "%$keyword%")
                ->orWhere('name', 'like', "%$keyword%");
        });
    }

    private function getEntityName() : string
    {
        return "Pays";
    }

    public function scopeCountries($query)
    {
        return $query->where('region', 'Africa');
    }

    public static function relations()
    {
        return [
            'states',
        ];
    }

    public static function secureDeleteRelations()
    {
        return [
            'states',
        ];
    }

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function borders()
    {
        return $this->hasMany(Border::class);
    }
}
