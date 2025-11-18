<?php

namespace Ntech\UserPackage\Models;

use App\Models\Space\SpaceRegistrationRequest;
use App\Models\Auth\Profile;
use App\Models\Config\ManagementCenter;
use App\Models\Config\Organization;
use App\Models\Order\Commission;
use App\Models\Plate\PlateOrder;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Staff extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        Notifiable,
        Filterable,
        SecureDelete;

    protected $fillable = [
        "position_id",
        "identity_id",
        "center_id",
        "invitation_id",
        "profile_id",
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Staff";
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new ScopeFilter('organization'),
            new ExactFilter('position_id'),
            new ExactFilter('identity_id'),
            new ExactFilter('profile_id'),
            new ExactFilter('invitation_id'),
            new Sort()
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->whereRelation('identity', 'npi', 'like', "%$keyword%")
                ->orWhereHas('identity', function ($q) use ($keyword) {
                    $keyword = strtolower($keyword);
                    $q->whereRaw("CONCAT(LOWER(firstname), ' ',LOWER(lastname)) LIKE ?", ["%$keyword%"])
                        ->orWhereRaw("CONCAT(LOWER(lastname), ' ',LOWER(firstname)) LIKE ?", ["%$keyword%"]);
                });
        });
    }

    public function scopeOrganization(Builder $query, string $keyword)
    {
        if (in_array($keyword, ['', 'null'])) {
            return $query;
        }

        return $query->where(function (Builder $query)  use ($keyword) {
            $query->whereRelation('organizations', 'name', 'like', "%$keyword%");
        });
    }

    public function validatedSpaceRegistrations()
    {
        return $this->hasMany(SpaceRegistrationRequest::class, 'validator_id');
    }

    public function rejectedSpaceRegistrations()
    {
        return $this->hasMany(SpaceRegistrationRequest::class, 'rejector_id');
    }

    public function createdSpaceRegistrations()
    {
        return $this->hasMany(SpaceRegistrationRequest::class, 'creator_id');
    }

    public function validatedPlateOrders()
    {
        return $this->hasMany(PlateOrder::class, 'validator_id');
    }

    public function rejectedPlateOrders()
    {
        return $this->hasMany(PlateOrder::class, 'rejector_id');
    }

    public function createdCommissions()
    {
        return $this->hasMany(Commission::class, 'created_by');
    }

    public static function secureDeleteRelations()
    {
        return [
            'validatedSpaceRegistrations',
            'rejectedSpaceRegistrations',
            'createdSpaceRegistrations',
            'validatedPlateOrders',
            'rejectedPlateOrders',
            'organizations',
        ];
    }

    public static function relations()
    {
        return [
            'position',
            'identity:id,firstname,lastname,email,telephone,npi',
            'identity.user:id,identity_id,email',
            'identity.user.roles:id,name,label',
            'organizations',
            'managementCenters'
        ];
    }

    public function identity()
    {
        return $this->belongsTo(Identity::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_staff')
            ->withPivot('position_id', 'created_at');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class)->with('roles');
    }

    public function managementCenters()
    {
        return $this->hasMany(ManagementCenter::class);
    }
}
