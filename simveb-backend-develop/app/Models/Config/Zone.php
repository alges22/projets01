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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Zone extends Model implements CanFilterContract
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
        'description'
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
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('code', 'like', "%$keyword%")
                ->orWhere('name', 'like', "%$keyword%");
        });
    }

    private function getEntityName() : string
    {
        return "Zones";
    }

    public static function relations()
    {
        return [
            'towns',
        ];
    }

    public static function secureDeleteRelations()
    {
        return [

        ];
    }

    public function towns()
    {
        return $this->belongsToMany(Town::class, );
    }

    /**
     * @return BelongsToMany
     */
    public function centers() : BelongsToMany
    {
        return $this->belongsToMany(ManagementCenter::class,'center_zone','zone_id','center_id');
    }

    /**
     * @return mixed|null
     */
    public function getCenter()
    {
        return $this->centers()->first();
    }

}
