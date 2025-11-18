<?php

namespace App\Models;

use App\Consts\Utils;
use App\Models\Order\Demand;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\HasFiles;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\SecureDelete;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;

class Mutation extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        HasRequiredDocumentTypes,
        Filterable,
        HasFiles,
        SoftDeletes;

    protected $fillable = [
        'sale_declaration_reference',
        'gray_card_id',
        'demand_id',
        'sale_declaration_id',
        'comment',
        'vehicle_owner_id',
        'vehicle_id',
        'new_owner_id',
    ];

    static function relations(): array
    {
        return [
            'grayCard',
            'saleDeclaration.vehicleOwner.identity',
            'saleDeclaration.certificate',
            'demand',
            'demand.vehicle',
            'demand.vehicleOwner'
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getEntityName(): string
    {
        return "Mutation";
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('sale_declaration_reference'),
            new ExactFilter('gray_card_id'),
            new ExactFilter('sale_declaration_id'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('sale_declaration_reference', 'like', "%$keyword%");
        });
    }

    public function saleDeclaration()
    {
        return $this->belongsTo(SaleDeclaration::class);
    }

    public function grayCard()
    {
        return $this->belongsTo(GrayCard::class);
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
        return $this->belongsTo(Demand::class, 'demand_id');
    }

    protected function blackListVerifiedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value  ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
}
