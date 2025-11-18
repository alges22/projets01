<?php

namespace App\Models;

use App\Models\Order\Demand;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\HasCertificate;
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
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;
use Spatie\Activitylog\LogOptions;

class GlassEngraving extends Model implements CanFilterContract
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
    public function getEntityName(): string
    {
        return "Gravage des vitres";
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

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleOwner(): BelongsTo
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    public function demand(): BelongsTo
    {
        return $this->belongsTo(Demand::class);
    }

}


