<?php

namespace App\Models;

use App\Interfaces\ModelHasRelations;
use App\Models\Order\Demand;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use App\Models\Vehicle\Vehicle;
use App\Traits\HasStatusLabel;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlateTransformation extends Model implements CanFilterContract, ModelHasRelations
{
    use HasUuids, HasFactory, HasStatusLabel, Filterable;

    protected $fillable = [
        'demand_id',
        'number',
        'status',
        'vehicle_id',
        'plate_color_id',
        'front_plate_shape_id',
        'back_plate_shape_id',
    ];

    protected $guarded = [];

    static function relations(): array
    {
        return [
            'demand',
            'vehicle',
            'plateColor',
            'frontPlateShape',
            'backPlateShape',
        ];
    }

    public function getEntityName(): string
    {
        return "Tranaformation de plaque";
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function plateColor()
    {
        return $this->belongsTo(PlateColor::class);
    }

    public function frontPlateShape()
    {
        return $this->belongsTo(PlateShape::class, 'front_plate_shape_id');
    }

    public function backPlateShape()
    {
        return $this->belongsTo(PlateShape::class, 'back_plate_shape_id');
    }

    public function getFilters()
    {
        return [
            new Sort,
            new ExactFilter('status'),
            new ExactFilter('demand_id'),
            new ExactFilter('vehicle_id'),
            new ExactFilter('plate_color_id'),
            new ExactFilter('front_plate_shape_id'),
            new ExactFilter('back_plate_shape_id'),
            new RelativeFilter('number'),
            new ScopeFilter('search'),
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($query) use ($keyword) {
            return $query->whereRelation('vehicle', 'vin', 'like', "%$keyword%");
        });
    }
}
