<?php

namespace App\Models\Config;

use App\Models\PoliceOfficer\PoliceOfficer;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
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

class Border extends Model
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'name',
        'longitude',
        'latitude',
        'border_country_id',
        'town_id',
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
            new RelativeFilter('border_country_id'),
            new RelativeFilter('town_id'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function ($query) use ($keyword) {
            return $query->where(function (Builder $query)  use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            });
        });
    }

    public static function relations()
    {
        return [
            'country', 'town'
        ];
    }

    private function getEntityName() : string
    {
        return "Frontière";
    }

    public static function secureDeleteRelations()
    {
        return [
            //
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'border_country_id');
    }

    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    /**
     * Get all of the officers for the Border
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function officers(): HasMany
    {
        return $this->hasMany(PoliceOfficer::class);
    }
}
