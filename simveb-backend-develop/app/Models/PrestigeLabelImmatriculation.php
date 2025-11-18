<?php

namespace App\Models;

use App\Interfaces\ModelHasRelations;
use App\Models\Config\Service;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Treatment\Treatment;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use App\Traits\HasTransactions;
use App\Traits\HasTreatments;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;

class PrestigeLabelImmatriculation extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
        HasUuids,
        HasRequiredDocumentTypes,
        Filterable,
        HasTreatments,
        HasTransactions,
        HasFiles,
        HasStatusLabel;

    protected $fillable = [
        'desired_label',
        'immatriculation_id',
        'payment_status',
        'status',
        'service_id',
        'active_treatment_id',
        'comment',
        'reference',
        'vehicle_owner_id'
    ];

    protected $casts = [];

    protected $guarded = [];

    static function relations(): array
    {
        return [
            'activeTreatment.service',
            'activeTreatment.responsible.identity:id,firstname,lastname',
            'activeTreatment.assignedToServiceBy.identity:id,firstname,lastname',
            'activeTreatment.assignedToStaffBy.identity:id,firstname,lastname',
            'treatments',
            'immatriculation',
            'vehicleOwner'
        ];
    }

    public function getFilters()
    {
        return [
            new Sort,
            new ScopeFilter('search'),
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($query) use ($keyword) {
            return $query->where('desired_label', 'like', "%$keyword%")
                ->orWhere('reference', 'like', "%$keyword%");
        });
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function activeTreatment()
    {
        return $this->belongsTo(Treatment::class, 'active_treatment_id');
    }

    public function immatriculation()
    {
        return $this->belongsTo(Immatriculation::class);
    }

    public function vehicleOwner()
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
