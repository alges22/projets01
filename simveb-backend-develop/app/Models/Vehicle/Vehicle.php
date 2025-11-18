<?php

namespace App\Models\Vehicle;

use App\Interfaces\ModelHasRelations;
use App\Models\Config\Country;
use App\Models\Config\OwnerType;
use App\Models\Config\Park;
use App\Models\GlassEngraving;
use App\Models\GrayCard;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Opposition;
use App\Models\Order\Demand;
use App\Models\Plate;
use App\Models\Pledge;
use App\Models\Title\TitleDeposit;
use App\Models\VehicleTransformation;
use App\Traits\HasImages;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory, HasUuids, Filterable, SecureDelete, HasImages;

    protected $fillable = [
        'origin_country_id',
        'customs_reference',
        'vin',
        'vehicle_brand_id',
        'vehicle_model',
        'number_of_seats',
        'vehicle_type_id',
        'vehicle_category_id',
        'owner_type_id',
        'owner_id',
        'engin_number',
        'charged_weight',
        'empty_weight',
        'first_circulation_year',
        'park_id',
        'is_transformed',
        'transformed_vehicle_id',
        'front_plate_id',
        'back_plate_id'
    ];

    protected $appends = [
        'vehicle_image'
    ];

    static function relations(): array
    {
        return [
            'grayCard',
            'characteristics',
            'vehicleType:id,name,label',
            'category:id,name,label,nb_plate,price',
            'brand:id,name,description',
            'originCountry',
            'park',
            'immatriculation.backPlateShape:id,name,code',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
            'immatriculationDemand'
        ];
    }

    public function characteristics()
    {
        return $this->belongsToMany(VehicleCharacteristic::class, 'vehicle_has_characteristics');
    }

    public function owner()
    {
        return $this->belongsTo(VehicleOwner::class, 'owner_id');
    }

    public function ownerType()
    {
        return $this->belongsTo(OwnerType::class, 'owner_type_id');
    }

    public function category()
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }

    public function immatriculationDemand()
    {
        return $this->hasOne(Demand::class);
    }

    public function immatriculation()
    {
        return $this->hasOne(Immatriculation::class);
    }

    public function immatriculations()
    {
        return $this->hasMany(Immatriculation::class);
    }

    public function grayCard()
    {
        return $this->hasOne(GrayCard::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function park()
    {
        return $this->belongsTo(Park::class);
    }

    public function brand()
    {
        return $this->belongsTo(VehicleBrand::class, 'vehicle_brand_id');
    }

    public function originCountry()
    {
        return $this->belongsTo(Country::class);
    }

    public function vehicleAdministrativeStatus()
    {
        return $this->hasOne(VehicleAdministrativeStatus::class);
    }

    public function titleDeposits(): HasMany
    {
        return $this->hasMany(TitleDeposit::class);
    }

    /**
     * Get all of the demands for the Vehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }

    public function govVehicle(): HasOne
    {
        return $this->hasOne(GovVehicle::class);
    }

    public function oppositions(): BelongsToMany
    {
        return $this->belongsToMany(Opposition::class, 'opposition_vehicle', 'vehicle_id', 'opposition_id')->withTimestamps();
    }

    public function getFilters()
    {
        return [
            new ScopeFilter('origin_country'),
            new Sort,
            new RelativeFilter('customs_reference'),
            new RelativeFilter('vin'),
            new RelativeFilter('vehicle_model'),
            new RelativeFilter('first_circulation_year'),
            new ExactFilter('first_circulation_year'),
            new ScopeFilter('search'),
        ];
    }

    public function scopeOriginCountry(Builder $query, string $keyword)
    {
        return $query->whereRelation('originCountry', 'name', 'like', "%$keyword%");
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            return $query->where('vin', 'like', "%$keyword%")
                ->orWhere('customs_reference', 'like', "%$keyword%")
                ->orWhereRelation('originCountry', 'name', 'like', "%$keyword%")
                ->orWhereRelation('immatriculation', 'number_label', 'like', "%$keyword%");
        });
    }

    public function getVehicleImageAttribute()
    {
        return  asset('img/vehicle.jpeg');
    }

    public function alerts()
    {
        return $this->hasMany(VehicleAlert::class);
    }

    public function frontPlate()
    {
        return $this->belongsTo(Plate::class, 'front_plate_id');
    }

    public function backPlate()
    {
        return $this->belongsTo(Plate::class, 'back_plate_id');
    }

    public function pledges()
    {
        return $this->hasMany(Pledge::class);
    }

    public function vehicleTransformations(): HasMany
    {
        return $this->hasMany(VehicleTransformation::class);
    }

    public function glassEngraving(): HasMany
    {
        return $this->hasMany(GlassEngraving::class);
    }
}
