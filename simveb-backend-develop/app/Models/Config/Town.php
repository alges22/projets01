<?php

namespace App\Models\Config;

use App\Traits\SecureDelete;
use Spatie\Activitylog\LogOptions;
use Baro\PipelineQueryCollection\Sort;
use App\Models\Institution\Institution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Baro\PipelineQueryCollection\ScopeFilter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;

class Town extends Model implements CanFilterContract
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
        'code',
        'state_id',
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
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        $keyword = strtolower($keyword);
        
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->whereRaw('LOWER(code) LIKE ?',  ["%$keyword%"])
                ->orWhereRaw('LOWER(name) LIKE ?',  ["%$keyword%"]);
        });
    }

    private function getEntityName() : string
    {
        return "Communes";
    }

    public static function relations()
    {
        return [
            'state',
            'zones',
            'districts',
            'institutions'
        ];
    }

    public static function secureDeleteRelations()
    {
        return [

        ];
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class);
    }

    public function getZone()
    {
        return $this->zones()->first();
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function parks()
    {
        return $this->belongsToMany(Park::class);
    }

    public function border()
    {
        return $this->hasOne(Border::class);
    }

    public function managementCenters()
    {
        return $this->hasMany(ManagementCenter::class);
    }

    public function institutions()
    {
        return $this->hasMany(Institution::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

}
