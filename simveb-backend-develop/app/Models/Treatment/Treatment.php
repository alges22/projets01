<?php

namespace App\Models\Treatment;

use App\Consts\Utils;
use App\Models\Auth\Profile;
use App\Models\Config\ManagementCenter;
use App\Models\Config\Organization;
use App\Models\Order\Demand;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Treatment extends Model implements CanFilterContract
{
    use HasFactory, HasUuids,  LogsActivity, ActivityTrait, Filterable, SecureDelete;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
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


    public function secureDeleteRelations()
    {
        return [];
    }

    public static function relations(): array
    {
        return [
            'responsible.identity:id,firstname,lastname',
            'assignedToServiceBy.identity:id,firstname,lastname',
            'assignedToStaffBy.identity:id,firstname,lastname',
            'assignedToInterpolStaffBy.identity:id,firstname,lastname',
            'interpolStaff.identity:id,firstname,lastname',
            'preValidatedBy.identity:id,firstname,lastname',
            'validatedBy.identity:id,firstname,lastname',
            'verifiedBy.identity:id,firstname,lastname',
            'closedBy.identity:id,firstname,lastname',
        ];
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function managementCenter()
    {
        return $this->belongsTo(ManagementCenter::class);
    }

    public function assignedToServiceBy()
    {
        return $this->belongsTo(Profile::class, 'assigned_to_service_by');
    }

    public function assignedToInterpolStaffBy()
    {
        return $this->belongsTo(Profile::class, 'assigned_to_interpol_staff_by');
    }

    public function interpolStaff()
    {
        return $this->belongsTo(Profile::class, 'interpol_staff_id');
    }
    public function assignedToStaffBy()
    {
        return $this->belongsTo(Profile::class, 'assigned_to_staff_by');
    }

    public function printedBy()
    {
        return $this->belongsTo(Profile::class, 'printed_by');
    }

    public function validatedBy()
    {
        return $this->belongsTo(Profile::class, 'validated_by');
    }

    public function preValidatedBy()
    {
        return $this->belongsTo(Profile::class, 'pre_validated_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(Profile::class, 'rejected_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Profile::class, 'verified_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(Profile::class, 'closed_by');
    }

    public function suspendedBy()
    {
        return $this->belongsTo(Profile::class, 'suspended_by');
    }

    public function responsible()
    {
        return $this->belongsTo(Profile::class, 'responsible_id');
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }

    protected function assignedToServiceAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }

    protected function assignedToStaffAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
}
