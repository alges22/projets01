<?php

namespace App\Models\Auth;

use App\Consts\Utils;
use App\Enums\ProfileTypesEnum;
use App\Models\Account\User;
use App\Models\Institution\Institution;
use App\Models\Opposition;
use App\Models\OppositionTreatment;
use App\Models\Pledge;
use App\Models\PledgeLift;
use App\Models\Space\Space;
use App\Models\Order\Demand;
use App\Models\Treatment\Treatment;
use App\Models\PledgeTreatment;
use App\Models\Vehicle\VehicleOwner;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Ntech\UserPackage\Models\Identity;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;

class Profile extends Model
{
    use HasFactory, HasUuids, HasRoles, HasPermissions, Filterable, CausesActivity, Notifiable;

    protected $guard_name = 'api';

    protected $fillable = [
        'user_id',
        'type_id',
        'space_id',
        'institution_id',
        'identity_id',
        'number',
        'suspended',
        'suspended_at',
    ];

    protected $appends = ['cart_demands'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(ProfileType::class, 'type_id');
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function pledgeLifts(): HasMany
    {
        return $this->hasMany(PledgeLift::class);
    }

    public function clerkPledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function clerkTreatments(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function oppositions(): HasMany
    {
        return $this->hasMany(Opposition::class);
    }

    public function pledgeTreatments(): HasMany
    {
        return $this->hasMany(PledgeTreatment::class);
    }

    public function isUserProfile()
    {
        return $this->type?->code == ProfileTypesEnum::user->name;
    }

    public function isAffiliateProfile()
    {
        return $this->type?->code == ProfileTypesEnum::affiliate->name;
    }

    public function getCartDemandsAttribute(): int
    {
        return getCart()?->demands()->count() ?? 0;
    }

    public function identity()
    {
        return $this->belongsTo(Identity::class);
    }

    /**
     * Get all of the demands for the Profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }

    public function activeTreatments()
    {
        return $this->hasMany(Treatment::class, 'responsible_id')
            ->whereNull('closed_at');
    }

    public function vehicleOwner()
    {
        return $this->hasOne(VehicleOwner::class);
    }
    public function lastAction(): MorphOne
    {
        return $this->morphOne(
            ActivitylogServiceProvider::determineActivityModel(),
            'causer'
        )->latest();
    }
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }

    public function oppositionTreatments(): HasMany
    {
        return $this->hasMany(OppositionTreatment::class, 'affected_to_clerk');
    }

    static function relations(): array
    {
        return [
            'type:id,name',
            'roles'
        ];
    }

    public function getFilters()
    {
        return [
            new ScopeFilter('npi'),
            new ScopeFilter('profile_id'),
            new ScopeFilter('search'),
            new Sort(),
        ];
    }

    /**
     *
     */
    public function scopeNpi(Builder $query, string $keyword)
    {
        return $query->whereRelation('identity', 'npi', 'like', "%$keyword%");
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($query) use ($keyword) {
            return $query->whereRelation('identity', 'npi', 'like', "%$keyword%")
                ->orWhereRelation('institution', 'ifu', 'like', "%$keyword%")
                ->orWhereRelation('type', 'name', 'like', "%$keyword%")
                ->orWhereHas('identity', function ($q) use ($keyword) {
                    $keyword = strtolower($keyword);
                    $q->whereRaw("CONCAT(LOWER(firstname), ' ',LOWER(lastname)) LIKE ?", ["%$keyword%"])
                        ->orWhereRaw("CONCAT(LOWER(lastname), ' ',LOWER(firstname)) LIKE ?", ["%$keyword%"]);
                });
        });
    }

    /**
     *
     */
    public function scopeProfileId(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->find($keyword);
        });
    }

    public function routeNotificationForMail()
    {
        return $this->identity->email;
    }

    public function routeNotificationForSms()
    {
        return $this->identity->telephone;
    }
}
