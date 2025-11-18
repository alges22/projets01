<?php

namespace App\Models\Vehicle;

use App\Consts\Utils;
use App\Interfaces\ModelHasRelations;
use App\Traits\HasStatusLabel;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GovVehicle extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory, HasUuids, Filterable, HasStatusLabel, SecureDelete;

    protected $fillable = [
        'customs_reference',
        'vin',
        'owner_npi',
        'status',
        'author_id',
        'vehicle_id',
        'institution_id'
    ];

    static function relations(): array
    {
        return [
            'vehicle'
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    /**
     * Get all the demands for the Vehicle
     *
     * @return BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getFilters(): array
    {
        return [
            new Sort,
            new RelativeFilter('customs_reference'),
            new RelativeFilter('vin'),
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('customs_reference', 'like', "%$keyword%")
                ->orWhere('vin', 'like', "%$keyword%");
        });
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }
}
