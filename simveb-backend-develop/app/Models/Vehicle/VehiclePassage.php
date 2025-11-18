<?php

namespace App\Models\Vehicle;

use App\Consts\Utils;
use App\Enums\VehiclePassageType;
use App\Enums\VehicleTypeAtBorder;
use App\Models\Auth\Profile;
use App\Models\Config\Border;
use App\Traits\SecureDelete;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Immatriculation\Immatriculation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Carbon\Carbon;

class VehiclePassage extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        Filterable,
        SoftDeletes;

    /**
     *
     */
    protected $fillable = [
        'officer_id',
        'vehicle_id',
        'foreign_vehicle_immatriculation_number',
        'immatriculation_country_id',
        'driver_firstname',
        'driver_lastname',
        'driver_telephone',
        'vehicle_owner_firstname',
        'vehicle_owner_lastname',
        'vehicle_provenance',
        'total_passengers_on_board',
        'passage_type',
        'driving_license_number',
        'gray_card_number',
        'driving_license_photo',
        'gray_card_photo',
        'border_id',
    ];

    /**
     *
     */
    protected $casts = [
        'driving_license_photo' => 'array',
        'gray_card_photo' => 'array',
    ];

    /**
     *
     */
    protected $appends = [
        'immatriculation_number',
        'passage_type_label',
        'vehicle_provenance_label',
        'created_date',
    ];

    /**
     *
     * @return array
     */
    static function relations(): array
    {
        return [
            'vehicle',
            'officer',
        ];
    }

    /**
     *
     * @return Spatie\ActivityLog\LogOptions
     */
    function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     *
     * @return string
     */
    private function getEntityName(): string
    {
        return 'Passage à la frontière de véhicule';
    }

    /**
     *
     * @return array
     */
    static function secureDeleteRelations(): array
    {
        return [];
    }

    /**
     *
     */
    public function getFilters()
    {
        return [
            //new ScopeFilter('immatriculation_number'),
            new Sort,
            new ScopeFilter('search'),
        ];
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($query) use ($keyword) {
            return $query->whereHas('vehicle', function ($q) use ($keyword) {
                $q->whereRelation('immatriculation', 'number_label', 'like', "%$keyword%");
            });
        });
    }

    /**
     * @
     */
  /*  public function scopeImmatriculationNumber(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query) use ($keyword) {
            $vehicleId = Immatriculation::where('number', $keyword)->pluck('vehicle_id')->first();
            $query->where('vehicle_id', $vehicleId);
        });
    }*/

    /**
     * Get the vehicle of the vehicle that passed
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the officer that store the passage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'officer_id');
    }

    /**
     * Get the border of the passage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function border(): BelongsTo
    {
        return $this->belongsTo(Border::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function getImmatriculationNumberAttribute()
    {
        return $this->attributes["foreign_vehicle_immatriculation_number"] ?? $this->vehicle->immatriculation->number_label;
    }

    /**
     *
     */
    protected function createdDate(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->attributes['created_at'])->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }

    /**
     *
     */
    protected function createdTime(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->attributes['created_at'])->translatedFormat(Utils::COMMON_TIME_FORMAT)
        );
    }

    /**
     *
     */
    protected function passageTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => VehiclePassageType::toNameValue()[$this->passage_type]
        );
    }
    protected function vehicleProvenanceLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => VehicleTypeAtBorder::toNameValue()[$this->vehicle_provenance]
        );
    }
}
