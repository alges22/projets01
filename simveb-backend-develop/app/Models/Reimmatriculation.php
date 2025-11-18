<?php

namespace App\Models;

use App\Models\Config\ReimmatriculationReason;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Order\Demand;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\HasStatusLabel;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimmatriculation extends Model implements CanFilterContract
{
    use HasUuids, HasFactory, HasStatusLabel, Filterable;

    protected $fillable = [
        'number',
        'reason_id',
        'demand_id',
        'status',
        'custom_reason',
        'vehicle_id',
        'vehicle_owner_id',
        'plate_color_id',
        'back_plate_shape_id',
        'front_plate_shape_id',
        'desired_number',
        'label',
        'immatriculation_id',
        'plate_transformation_id',
        'with_immatriculation'
    ];

    public function getEntityName(): string
    {
        return 'Ré-immatriculation';
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }

    public function reason()
    {
        return $this->belongsTo(ReimmatriculationReason::class, 'reason_id');
    }

    public function getFilters()
    {
        return [
            new Sort,
            new RelativeFilter('number'),
            new ExactFilter('reason_id'),
            new ExactFilter('demand_id'),
            new ExactFilter('status'),
            new RelativeFilter('custom_reason'),
            new ExactFilter('vehicle_id'),
            new ExactFilter('plate_color_id'),
            new ExactFilter('back_plate_shape_id'),
            new ExactFilter('front_plate_shape_id'),
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    static function relations()
    {
        return [
            'reason',
            'immatriculation',
            'plateTransformation',
            'plateColor',
            'frontPlateShape',
            'backPlateShape',
        ];
    }

    public function immatriculation()
    {
        return $this->belongsTo(Immatriculation::class);
    }

    public function plateTransformation()
    {
        return $this->belongsTo(PlateTransformation::class);
    }

    public function plateColor()
    {
        return $this->belongsTo(PlateColor::class);
    }

    public function frontPlateShape()
    {
        return $this->belongsTo(PlateShape::class);
    }

    public function backPlateShape()
    {
        return $this->belongsTo(PlateShape::class);
    }

    public function vehicleOwner()
    {
        return $this->belongsTo(VehicleOwner::class);
    }
}
