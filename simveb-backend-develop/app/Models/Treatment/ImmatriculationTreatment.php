<?php

namespace App\Models\Treatment;

use App\Models\Order\Demand;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Ntech\UserPackage\Models\Staff;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ImmatriculationTreatment extends Model implements CanFilterContract
{
    use HasFactory, HasUuids,  LogsActivity, ActivityTrait, Filterable, SecureDelete;

    protected $fillable = [
        "immatriculation_demand_id",
        'assigned_to_staff_at',
        'validated_at',
        'rejected_at',
        'suspended_at',
        'closed_at',
        'assigned_to_staff_by',
        'validated_by',
        'rejected_by',
        'suspended_by',
        'closed_by',
        'rejected_reason',
        'suspended_reason',
        'closed_reason',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName() : string
    {
        return "Traitement de demande d'immatriculation";
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort()
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            //
        });
    }

    public function immatriculationDemand(): BelongsTo
    {
        return $this->belongsTo(Demand::class);
    }
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Staff::class,'responsible_id');
    }


    public static function secureDeleteRelations()
    {
        return [
        ];
    }

    public static function relations() : array
    {
        return [
        ];
    }

}
