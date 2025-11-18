<?php

namespace App\Models;

use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\BooleanFilter;
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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PledgeLift extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, HasFiles, LogsActivity, Notifiable, Filterable, SecureDelete, SoftDeletes, HasStatusLabel;

    protected $fillable = [
        'status',
        'is_active',
        'author_id',
        'pledge_id',
        'institution_emitted_id',
        'pledge_lift_treatment_id',
        'can_update',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Levée de gage";
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('status'),
            new ExactFilter('pledge_id'),
            new ExactFilter('financial_institution_id'),
            new ExactFilter('institution_emitted_id'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->where(function (Builder $query) use ($keyword) {
            $query->whereHas('pledge', function (Builder $q) use ($keyword) {
                $q->where(function (Builder $subQuery) use ($keyword) {
                    $subQuery->whereRaw("LOWER(reference) LIKE ?", ["%$keyword%"]);

                    $subQuery->orWhereHas('financialInstitution', function (Builder $fiQuery) use ($keyword) {
                        $fiQuery->whereRaw("LOWER(name) LIKE ?", ["%$keyword%"])
                        ->orWhereRaw("LOWER(acronym) LIKE ?", ["%$keyword%"]);
                    });

                    $subQuery->orWhereHas('institutionEmitted', function (Builder $ieQuery) use ($keyword) {
                        $ieQuery->whereRaw("LOWER(name) LIKE ?", ["%$keyword%"])
                            ->orWhereRaw("LOWER(acronym) LIKE ?", ["%$keyword%"]);
                    });
                });
            })->orWhereRaw("LOWER(status) LIKE ?", ["%$keyword%"]);
        });
    }


    public static function relations(): array
    {
        return [
            'author:id,user_id,identity_id',
            'author.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'institutionEmitted:id,acronym,name,ifu,email,telephone,address',
            'activeTreatment',
            'activeTreatment.institutionTreat:id,acronym,name,ifu,email,telephone,address',
            'activeTreatment.treatedBy.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'activeTreatment.affectedToClerk.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'activeTreatment.affectedToAnatt.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'pledge',
            'pledge.activeTreatment',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
            //
        ];
    }

    public function pledge(): BelongsTo
    {
        return $this->belongsTo(Pledge::class);
    }

    public function institutionEmitted(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_emitted_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function activeTreatment(): HasOne
    {
        return $this->hasOne(PledgeLiftTreatment::class, 'id', 'pledge_lift_treatment_id');
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(PledgeLiftTreatment::class);
    }
}
