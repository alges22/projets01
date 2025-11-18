<?php

namespace App\Models;

use App\Enums\ProfileTypesEnum;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Institution\Institution;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use App\Models\Treatment\PrintOrder;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class Plate extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable, SoftDeletes;

    protected $fillable = [
        'serial_number',
        'rfid',
        'plate_shape_id',
        'plate_color_id',
        'anatt_order_id',
        'affiliate_order_id',
        'affiliate_id',
        'is_duplicate',
        'institution_order_id',
        'institution_id',
        'immatriculation_id',
    ];

    protected $casts = [];

    protected $guarded = [];

    protected $appends = ['in_affiliate_stock', 'in_anatt_stock', 'is_used'];

    public function plateShape()
    {
        return $this->belongsTo(PlateShape::class);
    }

    public function plateColor()
    {
        return $this->belongsTo(PlateColor::class);
    }

    static function relations()
    {
        return [
            'plateShape:id,name,description,cost',
            'plateColor:id,name,label,color_code,text_color,cost',
            'institution:id,name,email,ifu,telephone',
        ];
    }

    public function getFilters()
    {
        return [
            new Sort,
            new ExactFilter('institution_id'),
            new ExactFilter('plate_shape_id'),
            new ExactFilter('plate_color_id'),
            new RelativeFilter('serial_number'),
            new ScopeFilter('search'),
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        $query->where(function ($q) use ($keyword) {
            $q->where('serial_number', 'like', "%$keyword%");
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Plaque";
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function immatriculation()
    {
        return $this->belongsTo(Immatriculation::class);
    }

    public function duplicatePlate()
    {
        return $this->hasOne(PlateDuplicate::class, 'old_plate_id');
    }

    public function printOrders()
    {
        return $this->belongsToMany(PrintOrder::class);
    }

    public function getInAffiliateStockAttribute()
    {
        return empty($this->rfid) && !empty($this->institution_id) && $this->institution->space->profileType->code != ProfileTypesEnum::anatt->name;
    }

    public function getInAnattStockAttribute(): bool
    {
        return empty($this->rfid) && (empty($this->institution_id) || !empty($this->institution_id) && $this->institution->space->profileType->code == ProfileTypesEnum::anatt->name);
    }

    public function getIsUsedAttribute()
    {
        return !empty($this->rfid);
    }
}
