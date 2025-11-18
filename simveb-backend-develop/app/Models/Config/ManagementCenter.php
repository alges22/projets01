<?php

namespace App\Models\Config;

use App\Models\Auth\Profile;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
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

class ManagementCenter extends Model
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
        'manager_title',
        'town_id',
        'state_id',
        'district_id',
        'village_id',
        'responsible_id',
        'management_center_type_id',
        'staff_id',
    ];

    protected $casts = [];

    protected $guarded = [];

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
            new RelativeFilter('town_id'),
            new RelativeFilter('state_id'),
            new RelativeFilter('village_id'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('town_id', 'like', "%$keyword%")
                ->orWhere('state_id', 'like', "%$keyword%")
                ->orWhere('village_id', 'like', "%$keyword%");
        });
    }

    public static function relations()
    {
        return [
            'services', 'ownerTypes', 'parks', 'town', 'state', 'district', 'village', 'responsible', 'type'
        ];
    }

    private function getEntityName() : string
    {
        return "Centre de gestion";
    }

    public static function secureDeleteRelations()
    {
        return [
            //
        ];
    }

    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function responsible()
    {
        return $this->belongsTo(Profile::class, 'responsible_id');
    }

    public function type()
    {
        return $this->belongsTo(ManagementCenterType::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class,'center_service','center_id','service_id');
    }

    public function ownerTypes()
    {
        return $this->belongsToMany(OwnerType::class);
    }

    public function parks()
    {
        return $this->belongsToMany(Park::class);
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class,'center_zone','center_id','zone_id');
    }
}
