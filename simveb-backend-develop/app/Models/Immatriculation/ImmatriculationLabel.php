<?php

namespace App\Models\Immatriculation;

use App\Interfaces\ModelHasRelations;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use App\Models\PrestigeLabelImmatriculation;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ImmatriculationLabel extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
        HasUuids,
        Filterable,
        ActivityTrait,
        LogsActivity,
        SecureDelete;

    protected $fillable = [
        'label',
        'demand_id',
        'immatriculation_id',
        'plate_color_id',
        'front_plate_shape_id',
        'back_plate_shape_id',
        'status'
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
            $query->where('label', 'like', "%$keyword%")
                ->orWhere('immatriculation_id', 'like', "%$keyword%");
        });
    }

    public function getEntityName(): string
    {
        return "Immatriculation Label";
    }

    static function relations(): array
    {
        return [
            'immatriculation',
            'prestigeLabelImmatriculation',
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

    public function prestigeLabelImmatriculation()
    {
        return $this->belongsTo(PrestigeLabelImmatriculation::class);
    }

    public function frontPlateShape()
    {
        return $this->belongsTo(PlateShape::class, 'front_plate_shape_id');
    }

    public function backPlateShape()
    {
        return $this->belongsTo(PlateShape::class, 'back_plate_shape_id');
    }

    public function plateColor()
    {
        return $this->belongsTo(PlateColor::class, 'plate_color_id');
    }
}
