<?php

namespace App\Models;

use App\Interfaces\ModelHasRelations;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Order\Demand;
use App\Models\Treatment\PrintOrder;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class GrayCard extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
        HasUuids,
        Filterable,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        SoftDeletes;

    protected $fillable = [
        'number',
        'immatriculation_id',
        'vehicle_id',
        'vehicle_owner_id',
        'is_lost',
        'is_spoiled',
        'comment',
        'deactivated_at',
        'deactivation_reason',
        'print_order_id',
    ];

    protected $guarded = [];

    protected $casts = [
        'is_lost' => 'boolean',
        'is_spoiled' => 'boolean',
        'deactivated_at' => 'datetime'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('number', 'like', "%$keyword%")
                ->orWhere('immatriculation_id', 'like', "%$keyword%");
        });
    }

    static function relations(): array
    {
        return [
            'immatriculation',
            'vehicle',
            'vehicleOwner'
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function immatriculation()
    {
        return $this->belongsTo(Immatriculation::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleOwner()
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }

    private function getEntityName(): string
    {
        return 'Carte grise';
    }

    public function printOrder()
    {
        return $this->belongsTo(PrintOrder::class);
    }

    static function generateNumber(): string
    {
        return time() . random_int(1000, 9999);
    }
}
