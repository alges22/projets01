<?php

namespace App\Models;

use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\BooleanFilter;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pledge extends Model
{
    use HasFactory, HasUuids, ActivityTrait, HasFiles, LogsActivity, Notifiable, Filterable, SecureDelete, SoftDeletes, HasStatusLabel;

    protected $fillable = [
        'status',
        'reference',
        'financial_institution',
        'vehicle_id',
        'vehicle_owner_id',
        'author_id',
        'institution_emitted_id',
        'pledge_treatment_id',
        'is_active',
        'can_update',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Gage";
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new ScopeFilter('firstname'),
            new ScopeFilter('lastname'),
            new ScopeFilter('institution_emitted_name'),
            new ScopeFilter('institution_treat_name'),
            new Sort(),
            new RelativeFilter('status'),
            new ExactFilter('reference'),
            new BooleanFilter('is_active'),
            new ExactFilter('vehicle_owner_id'),
            new ExactFilter('author_id'),
            new ExactFilter('institution_emitted_id'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('status', 'like', "%$keyword%")
                ->orWhere('reference', 'like', "%$keyword%")
                ->orWhere('is_active', 'like', "%$keyword%");
        });
    }

    public function scopeFirstname(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->withWhereHas('author', function ($q) use ($keyword) {
            $q->withWhereHas('identity', function ($k) use ($keyword) {
                $k->whereRaw('LOWER(firstname) LIKE ?', $keyword);
            });
        });
    }

    public function scopeLastname(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->withWhereHas('author', function ($q) use ($keyword) {
            $q->withWhereHas('identity', function ($k) use ($keyword) {
                $k->whereRaw('LOWER(lastname) LIKE ?', $keyword);
            });
        });
    }

    public function scopeInstitutionEmittedName(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->withWhereHas('institutionEmitted', function ($q) use ($keyword) {
            $q->where('name', $keyword);
        });
    }

    public function scopeInstitutionTreatName(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->withWhereHas('institutionTreat', function ($q) use ($keyword) {
            $q->where('name', $keyword);
        });
    }

    public static function relations(): array
    {
        return [
            'vehicle',
            'vehicleOwner',
            'author:id,user_id,identity_id',
            'author.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'vehicleOwner.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'vehicleOwner.institution:id,acronym,name,ifu,email,telephone,address',
            'institutionEmitted:id,acronym,name,ifu,email,telephone,address',
            'financialInstitution:id,acronym,name',
            'activeTreatment',
            'activeTreatment.institutionTreat:id,acronym,name,ifu,email,telephone,address',
            'activeTreatment.treatedBy.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'activeTreatment.affectedToInstitution.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'activeTreatment.affectedToClerk.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'activeTreatment.affectedToAnatt.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'pledgeLift',
            'pledgeLift.activeTreatment',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
            //
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleOwner(): BelongsTo
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function clerkProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'affected_to_clerk');
    }

    public function activeTreatment(): HasOne
    {
        return $this->hasOne(PledgeTreatment::class, 'id', 'pledge_treatment_id');
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(PledgeTreatment::class);
    }

    public function institutionTreat(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_treat_id');
    }

    public function institutionEmitted(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_emitted_id');
    }

    public function financialInstitution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'financial_institution');
    }

    public function pledgeLift(): HasOne
    {
        return $this->hasOne(PledgeLift::class);
    }
}
