<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Models\Order\Demand;
use App\Traits\SecureDelete;
use App\Traits\HasStatusLabel;
use App\Models\Vehicle\Vehicle;
use Spatie\Activitylog\LogOptions;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\HasCertificate;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Model;
use Baro\PipelineQueryCollection\ExactFilter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Baro\PipelineQueryCollection\BooleanFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;

class TintedWindowAuthorization extends Model implements CanFilterContract
{
    use HasFactory, Filterable, HasUuids, SecureDelete, SoftDeletes, HasRequiredDocumentTypes, HasFiles, HasStatusLabel, HasCertificate;

    protected $fillable = [
        'number',
        'vehicle_id',
        'vehicle_owner_id',
        'demand_id',
        'status',
        'expired',
        'issued_at',
        'expired_at',
    ];

    protected $guarded = [
        'issued_at',
        'expired_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expired_at' => 'datetime'
    ];

    /**
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters()
    {
        return [
            new Sort,
            new ExactFilter('vehicle_id'),
            new ExactFilter('vehicle_owner_id'),
            new ExactFilter('demand_id'),
            new BooleanFilter('expired'),
            new RelativeFilter('status'),
            new ScopeFilter('search'),
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($query) use ($keyword) {
            return $query->whereRelation('vehicle', 'vin', 'like', "%$keyword%");
        });
    }

    /**
     * @return string
     */
    private function getEntityName(): string
    {
        return "Autorisation de vitres teintées";
    }

    /**
     * @return array
     */
    public static function relations(): array
    {
        return [
            'demand',
            'vehicle.immatriculation',
            'vehicleOwner',
            'certificate',
        ];
    }

    /**
     * @return array
     */
    public static function secureDeleteRelations()
    {
        return [];
    }

    /**
     * Get the vehicle which is authorized to have its windows tinted
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     *
     */
    public function vehicleOwner(): BelongsTo
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    /**
     *
     */
    public function demand(): BelongsTo
    {
        return $this->belongsTo(Demand::class);
    }
}
