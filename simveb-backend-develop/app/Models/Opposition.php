<?php

namespace App\Models;

use App\Models\Auth\Profile;
use App\Models\Config\TitleReason;
use App\Models\Institution\Institution;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Opposition extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, HasFiles, LogsActivity, Notifiable, Filterable, SecureDelete, SoftDeletes, HasStatusLabel;

    protected $fillable = [
        'status',
        'reference',
        'reason_for_opposition',
        'owner_id',
        'author_id',
        'institution_id',
        'treatment_id',
        'is_active',
    ];

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('status', 'like', "%$keyword%")
                ->orWhere('reference', 'like', "%$keyword%");
        });
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('status'),
            new ExactFilter('reference'),
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Opposition";
    }

    public static function relations(): array
    {
        return [
            'vehicles',
            'owner.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'owner.institution:id,acronym,name,ifu,email,telephone,address',
            'titleReason:id,label',
            'author.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'institution:id,acronym,name,ifu,email,telephone,address',
            'activeTreatment',
            'activeTreatment.affectedToClerk',
            'activeTreatment.affectedToClerk.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'activeTreatment.affectedToClerk.institution:id,acronym,name,ifu,email,telephone,address',
            'activeTreatment.affectedToJudge',
            'activeTreatment.affectedToJudge.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'activeTreatment.affectedToJudge.institution:id,acronym,name,ifu,email,telephone,address',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
            //
        ];
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'opposition_vehicle', 'opposition_id', 'vehicle_id')->withTimestamps();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(VehicleOwner::class, 'owner_id');
    }

    public function activeTreatment(): HasOne
    {
        return $this->hasOne(OppositionTreatment::class, 'id', 'treatment_id');
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(OppositionTreatment::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function titleReason(): BelongsTo
    {
        return $this->belongsTo(TitleReason::class, 'reason_for_opposition');
    }

}
