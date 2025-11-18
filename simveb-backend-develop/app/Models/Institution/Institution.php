<?php

namespace App\Models\Institution;

use App\Models\Account\Declarant;
use App\Models\Config\Border;
use App\Models\Config\District;
use App\Models\Config\Town;
use App\Models\Config\Village;
use App\Models\Opposition;
use App\Models\Order\Demand;
use App\Models\Pledge;
use App\Models\PledgeLift;
use App\Models\PledgeTreatment;
use App\Models\Space\Space;
use App\Models\Vehicle\VehicleAdministrativeStatus;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Institution extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        Filterable,
        SoftDeletes,
        Notifiable;

    protected $fillable = [
        'acronym',
        'name',
        'social_reason',
        'ifu',
        'rccm',
        'email',
        'telephone',
        'address',
        'type_id',
        'town_id',
        'district_id',
        'village_id',
        'logo_path',
        'border_id',
        'profile_type_code',
    ];

    protected $casts = [
        'logo_path' => 'array',
    ];

    protected $appends = [
        'logo',
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
            new RelativeFilter('acronym'),
            new RelativeFilter('name'),
            new RelativeFilter('address'),
            new RelativeFilter('telephone'),
            new RelativeFilter('email'),
            new RelativeFilter('ifu'),
            new ExactFilter('type_id')
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('acronym', 'like', "%$keyword%")
                ->orWhere('name', 'like', "%$keyword%")
                ->orWhere('email', 'like', "%$keyword%")
                ->orWhere('ifu', 'like', "%$keyword%")
                ->orWhere('telephone', 'like', "%$keyword%")
                ->orWhere('address', 'like', "%$keyword%");
        });
    }

    private function getEntityName(): string
    {
        return "Institutions";
    }

    public static function relations()
    {
        return [
            'type',
            'town',
            'district',
            'village',
        ];
    }

    public static function secureDeleteRelations()
    {
        return [];
    }

    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }

    public function type()
    {
        return $this->belongsTo(InstitutionType::class, 'type_id');
    }

    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function declarants()
    {
        return $this->hasMany(Declarant::class);
    }

    public function VehicleAdministrativeStatuses()
    {
        return $this->hasMany(VehicleAdministrativeStatus::class);
    }

    public function pledgeLifts(): HasMany
    {
        return $this->hasMany(PledgeLift::class);
    }

    public function oppositions(): HasMany
    {
        return $this->hasMany(Opposition::class);
    }

    public function pledgeTreatment(): HasMany
    {
        return $this->hasMany(PledgeTreatment::class, 'institution_treat_id');
    }

    public function pledgeTreatmentEmitted(): HasMany
    {
        return $this->hasMany(PledgeTreatment::class, 'institution_emitted_id');
    }

    public function pledgeTreatmentRemitted(): HasMany
    {
        return $this->hasMany(PledgeTreatment::class, 'institution_remitted_id');
    }

    public function treatedPledges(): HasMany
    {
        return $this->hasMany(Pledge::class, 'institution_treat_id');
    }

    public function emittedPledges(): HasMany
    {
        return $this->hasMany(Pledge::class, 'institution_emitted_id');
    }

    public function financialInstitutionPledges(): HasMany
    {
        return $this->hasMany(Pledge::class, 'financial_institution');
    }

    public function space()
    {
        return $this->hasOne(Space::class);
    }

    public function border()
    {
        return $this->belongsTo(Border::class);
    }

    public function getLogoAttribute()
    {
        if (isset($this->attributes['logo_path'])) {
            return asset(json_decode($this->attributes['logo_path'])->path);
        }
        return null;
    }

    public function routeNotificationForMail()
    {
        return $this->email;
    }

    public function routeNotificationForSms()
    {
        return $this->telephone;
    }
}
