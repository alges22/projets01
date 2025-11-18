<?php

namespace App\Models\Config;

use App\Models\Institution\Institution;
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

class Village extends Model implements CanFilterContract
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
        'district_id'
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
        return "Villages";
    }

    public static function relations()
    {
        return [
            'district',
            'institutions'
        ];
    }

    public static function secureDeleteRelations()
    {
        return [

        ];
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function institutions()
    {
        return $this->hasMany(Institution::class);
    }
}
