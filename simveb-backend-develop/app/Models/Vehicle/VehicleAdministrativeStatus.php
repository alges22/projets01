<?php

namespace App\Models\Vehicle;

use App\Interfaces\ModelHasRelations;
use App\Models\Account\Declarant;
use App\Models\Institution\Institution;
use App\Models\Order\Demand;
use App\Traits\HasFiles;
use App\Traits\HasOtpCodes;
use App\Traits\HasStatusLabel;
use App\Traits\HasTransactions;
use App\Traits\HasTreatments;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;

class VehicleAdministrativeStatus extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
        HasUuids,
        HasRequiredDocumentTypes,
        Filterable,
        HasTreatments,
        HasTransactions,
        HasFiles,
        HasStatusLabel,
        HasOtpCodes,
        SecureDelete,
        SoftDeletes;

    protected $fillable = [
        'situation_type',
        'motif',
        'declaration_code',
        'vehicle_id',
        'declarant_id',
        'vehicle_owner_id',
        'institution_id',
        'demand_id',
        'status'
    ];

    static function relations(): array
    {
        return [
            'vehicle',
            'declarant:id,identity_id,institution_id',
            'declarant.identity:id,firstname,lastname,email,telephone,ifu,npi',
            'declarant.institution:id,acronym,name,ifu,email,telephone,address',
            'vehicleOwner.identity:id,firstname,lastname,email,telephone,ifu,npi',
            'institution:id,acronym,name,ifu,email,telephone,address,town_id,district_id,village_id,institution_type_id',
            'institution.village:id,code,name',
            'institution.institutionType:id,name,description'
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
        return $query->where(function ($query)  use ($keyword) {
            return $query->whereRelation('vehicle', 'vin', 'like', "%$keyword%")
                ->orWhere('declaration_code', 'like', "%$keyword%");
        });
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function declarant()
    {
        return $this->belongsTo(Declarant::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleOwner()
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }
}
