<?php

namespace App\Models;

use App\Interfaces\ModelHasRelations;
use App\Models\Institution\Institution;
use App\Models\Order\Demand;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Services\IdentityService;
use App\Services\VehicleOwnerService;
use App\Traits\HasCertificate;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use App\Traits\HasTransactions;
use App\Traits\HasTreatments;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;
use Ntech\UserPackage\Models\Identity;

class SaleDeclaration extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
        HasUuids,
        HasRequiredDocumentTypes,
        Filterable,
        HasTreatments,
        HasTransactions,
        HasFiles,
        HasStatusLabel;
    use HasCertificate {
        generateCertificate as private myGenerateCertificate;
    }

    protected $fillable = [
        'reference',
        'new_owner_id',
        'new_owner_npi',
        'new_owner_ifu',
        'vehicle_owner_id',
        'vehicle_id',
        'demand_id',
        'issued_at',
    ];

    static function relations(): array
    {
        return [
            // 'newOwner.identity',
            'demand',
            'vehicle',
            'vehicleOwner.identity',
            'certificate',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function getEntityName(): string
    {
        return "Vente de véhicle";
    }

    public function newOwner()
    {
        return $this->belongsTo(VehicleOwner::class, 'new_owner_id');
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class, 'demand_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleOwner()
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    public function generateCertificate($stream = true)
    {
        $vehicleOwnerService = new VehicleOwnerService();

        $ownerData = [
            'npi' => $this->vehicleOwner->identity?->npi,
            'ifu' => $this->vehicleOwner->space?->ifu
        ];
        $owner = $vehicleOwnerService->getVehicleOwner($ownerData)->response()->getData(true);

        $newOwnerData = ['npi' => $this->new_owner_npi, 'ifu' => $this->new_owner_ifu];
        $newOwner = $vehicleOwnerService->getVehicleOwner($newOwnerData)->response()->getData(true);
        $data = ['owner' => $owner['data'], 'newOwner' => $newOwner['data'], 'model' => $this];

        return $this->myGenerateCertificate($stream, null, $data);
    }

    public function getBuyerAttribute()
    {
        if ($this->new_owner_npi) {
            return (new IdentityService)->getIdentityByNpi($this->new_owner_npi);
        } elseif ($this->new_owner_ifu) {
            return (new IdentityService)->getIdentityByIfu($this->new_owner_ifu);
        } else {
            return null;
        }
    }

    public function getBuyerNotifiableAttribute(): Identity|Institution|null
    {
        if ($this->new_owner_npi) {
            return Identity::where('npi', $this->new_owner_npi)->first();
        } elseif ($this->new_owner_ifu) {
            return Institution::where('ifu', $this->new_owner_ifu)->first();
        } else {
            return null;
        }
    }
}
