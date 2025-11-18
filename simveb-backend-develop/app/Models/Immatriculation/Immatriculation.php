<?php

namespace App\Models\Immatriculation;

use App\Interfaces\ModelHasRelations;
use App\Models\GrayCard;
use App\Models\Order\Demand;
use App\Models\Plate;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;

class Immatriculation extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory, HasUuids, Filterable, ActivityTrait;

    protected $with = [
        'grayCard',
        'activeGrayCard',
    ];

    protected $fillable = [
        'demand_id',
        'number',
        'vehicle_id',
        'vehicle_owner_id',
        'issued_at',
        'immatriculation_format_id',
        'number_label',
        'plate_color_id',
        'front_plate_shape_id',
        'back_plate_shape_id',
        'desired_number',
        'label',
        'number_template_id',
        'status',
        'prefix',
        'alphabetic_label',
        'zone',
        'numeric_label',
        'country_code',
    ];


    protected $casts = ['number' => 'array'];

    protected $appends = ['formatLabel'];

    static function relations(): array
    {
        return [
            'frontPlateShape:id,name,code',
            'backPlateShape:id,name,code',
            'plateColor:id,name,label,color_code,text_color'
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function getEntityName(): string
    {
        return "Immatriculation";
    }

    public function getFormatLabelAttribute()
    {
        $format = '';
        // if ($this->number){
        //     foreach ($this->number as $key => $value)
        //     {
        //         $format .= $value. ' ';
        //     }
        // }

        return trim($format);
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
        return $query->where(function ($query) use ($keyword) {
            return $query->where('number_label', 'like', "%$keyword%")
                ->orWhereRelation('vehicle', 'vin', 'like', "%$keyword%")
                ->orWhereRelation('demand', 'reference', 'like', "%$keyword%")
            ;
        });
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function grayCard()
    {
        return $this->hasOne(GrayCard::class);
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class);
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

    public function vehicleOwner()
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    public function plates()
    {
        return $this->hasMany(Plate::class);
    }

    public function activeGrayCard()
    {
        return $this->hasOne(GrayCard::class)->latest()->where('is_active', true);
    }
}
