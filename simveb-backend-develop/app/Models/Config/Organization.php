<?php

namespace App\Models\Config;

use App\Models\Auth\Profile;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Database\Factories\Config\OrganizationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Ntech\UserPackage\Models\Staff;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

class Organization extends Model implements CanFilterContract
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
        'description',
        'parent_id',
        'is_interpol',
        'responsible_id',
    ];

    protected $casts = ['is_interpol' => 'boolean'];

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
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('description', 'like', "%$keyword%")
                ->orWhere('name', 'like', "%$keyword%");
        });
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'organization_staff')
            ->withPivot('position_id');
    }

    private function getEntityName() : string
    {
        return "Service";
    }

    public static function relations()
    {
        return [
            'parent',
            'staff:id,profile_id,identity_id',
            'staff.identity:id,firstname,lastname,telephone',
        ];
    }

    public static function secureDeleteRelations()
    {
        return [
            'staff',
            'parent',
        ];
    }

    public function services()
    {
        return $this->hasMany(Organization::class);
    }

    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

     public function responsible()
     {
         return $this->belongsTo(Profile::class,'responsible_id');
     }

     public static function newFactory(): Factory
     {
        return OrganizationFactory::new();
     }
}
