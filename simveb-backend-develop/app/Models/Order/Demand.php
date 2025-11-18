<?php

namespace App\Models\Order;

use App\Consts\AvailableServiceType;
use App\Consts\Utils;
use App\Enums\Status;
use App\Exceptions\UnknownServiceException;
use App\Interfaces\ModelHasRelations;
use App\Models\DemandUpdatesHistory;
use App\Models\GlassEngraving;
use App\Models\Institution\Institution;
use App\Models\Auth\Profile;
use App\Models\Config\Service;
use App\Models\DemandAction;
use App\Models\DemandOtp;
use App\Models\GrayCardDuplicate;
use App\Models\Immatriculation\Immatriculation;
use App\Models\PlateDuplicate;
use App\Models\SaleDeclaration;
use App\Models\Immatriculation\ImmatriculationLabel;
use App\Models\Treatment\PrintOrder;
use App\Models\Treatment\Treatment;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleOwner;
use App\Models\VehicleTransformation;
use App\Services\Demand\DemandHandlerService;
use App\Traits\HasFiles;
use App\Traits\HasOtpCodes;
use App\Traits\HasStatusLabel;
use App\Traits\HasTreatments;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\DateFromFilter;
use Baro\PipelineQueryCollection\DateToFilter;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Demand extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
        HasUuids,
        Filterable,
        HasTreatments,
        HasFiles,
        HasStatusLabel,
        HasOtpCodes,
        SoftDeletes;

    protected $appends = [
        'step',
        'is_editable',
        'is_delayed',
        'delayed_hours',
    ];

    protected $fillable = [
        'reference',
        'institution_id',
        'vehicle_owner_id',
        'vehicle_id',
        'active_treatment_id',
        'profile_id',
        'black_listed_at',
        'submitted_at',
        'black_list_verified_at',
        'status',
        'service_id',
        'payment_status',
        'model_type',
        'model_id',
        'otp_verified',
        'characteristic_transformation',
        'updates_status',
    ];


    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new ScopeFilter('management_center'),
            new ScopeFilter('organization'),
            new ScopeFilter('vin'),
            new ScopeFilter('npi'),
            new ScopeFilter('ifu'),
            new ScopeFilter('lastname'),
            new ScopeFilter('firstname'),
            new ScopeFilter('institution_name'),
            new Sort(),
            new RelativeFilter('status'),
            new RelativeFilter('payment_status'),
            new ExactFilter('reference'),
            new ExactFilter('institution_id'),
            new ExactFilter('profile_id'),
            new ExactFilter('vehicle_owner_id'),
            new ExactFilter('vehicle_id'),
            new ExactFilter('service_id'),
            new DateFromFilter('created_at'),
            new DateFromFilter('submitted_at'),
            new DateFromFilter('black_listed_at'),
            new DateFromFilter('black_list_verified_at'),
            new DateFromFilter('paid_at'),
            new DateToFilter('created_at'),
            new DateToFilter('submitted_at'),
            new DateToFilter('black_listed_at'),
            new DateToFilter('black_list_verified_at'),
            new DateToFilter('paid_at'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        $keyword = trim($keyword);

        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('reference', 'like', "%$keyword%")
                ->orWhereRelation('vehicle', 'vin', 'like', "%$keyword%")
                ->orWhereHas('vehicleOwner', function ($q) use ($keyword) {
                    $q->whereRelation('identity', 'npi', 'like', "%$keyword%")
                        ->whereRelation('institution', 'ifu', 'like', "%$keyword%");
                });
        });
    }

    public function scopeManagementCenter(Builder $query, string $keyword)
    {
        // TO-DO:Check existence of $keyword or add a suitable failure case

        return $query->withWhereHas('treatments', function ($q) use ($keyword) {
            $q->where('management_center_id', $keyword);
        });
    }

    public function scopeFirstname(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->withWhereHas('vehicleOwner', function ($q) use ($keyword) {
            $q->withWhereHas('identity', function ($k) use ($keyword) {
                $k->whereRaw('LOWER(firstname) LIKE ?', $keyword);
            });
        });
    }

    public function scopeLastname(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->withWhereHas('vehicleOwner', function ($q) use ($keyword) {
            $q->withWhereHas('identity', function ($k) use ($keyword) {
                $k->whereRaw('LOWER(lastname) LIKE ?', $keyword);
            });
        });
    }

    public function scopeInstitutionName(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->withWhereHas('vehicleOwner', function ($q) use ($keyword) {
            $q->withWhereHas('institution', function ($k) use ($keyword) {
                $k->whereRaw('LOWER(social_reason) LIKE ?', $keyword)
                    ->orWhereRaw('LOWER(name) LIKE ?', $keyword);
            });
        });
    }

    public function scopeNpi(Builder $query, string $keyword)
    {
        // TO-DO:Check existence of $keyword or add a suitable failure case

        return $query->withWhereHas('vehicleOwner', function ($q) use ($keyword) {
            $q->withWhereHas('identity', function ($k) use ($keyword) {
                $k->where('npi', $keyword);
            });
        });
    }

    public function scopeIfu(Builder $query, string $keyword)
    {
        // TO-DO:Check existence of $keyword or add a suitable failure case

        return $query->withWhereHas('vehicleOwner', function ($q) use ($keyword) {
            $q->withWhereHas('institution', function ($k) use ($keyword) {
                $k->where('ifu', $keyword);
            });
        });
    }

    public function scopeVin(Builder $query, string $keyword)
    {
        // TO-DO:Check existence of $keyword or add a suitable failure case

        return $query->withWhereHas('vehicle', function ($q) use ($keyword) {
            $q->where('vin', $keyword);
        });
    }

    public function scopeOrganization(Builder $query, string $keyword)
    {
        return $query->withWhereHas('treatments', function ($q) use ($keyword) {
            $q->where('organization_id', $keyword);
        });
    }

    protected $casts = [
        'black_listed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'black_list_verified_at' => 'datetime'
    ];


    static function relations(): array
    {
        return [
            'vehicle',
            'activeTreatment.responsible.identity:id,firstname,lastname',
            'activeTreatment.assignedToServiceBy.identity:id,firstname,lastname',
            'activeTreatment.assignedToStaffBy.identity:id,firstname,lastname',
            'activeTreatment.assignedToInterpolStaffBy.identity:id,firstname,lastname',
            'activeTreatment.interpolStaff.identity:id,firstname,lastname',
            'activeTreatment.preValidatedBy.identity:id,firstname,lastname',
            'activeTreatment.validatedBy.identity:id,firstname,lastname',
            'activeTreatment.verifiedBy.identity:id,firstname,lastname',
            'activeTreatment.closedBy.identity:id,firstname,lastname',

            'vehicle.immatriculation',
            'vehicle.vehicleType:id,label',
            'vehicle.category:id,label,nb_plate',
            'vehicle.brand:id,name,description',
            'vehicle.originCountry:id,name',
            'vehicleOwner.legalStatus:id,name,description',
            'vehicleOwner.ownerType:id,label',
            'immatriculation.grayCard',
            'immatriculation',
            'immatriculation_label',
            'author',
            'service',
            'extraServices',
            'transformation',
            'transformation.transformationCharacteristics:id,transformation_id,old_characteristic,new_characteristic',
            'transformation.transformationCharacteristics.oldCharacteristic:id,value,category_id',
            'transformation.transformationCharacteristics.newCharacteristic:id,value,category_id',
            'transformation.transformationCharacteristics.oldCharacteristic.category:id,name',
            'transformation.transformationCharacteristics.newCharacteristic.category:id,name',
            'transformation.transformationCharacteristics.newCharacteristic.category.types:id,label',
            'transformation.transformationCharacteristics.oldCharacteristic.category.types:id,label',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function activeTreatment()
    {
        return $this->belongsTo(Treatment::class, 'active_treatment_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function author()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function vehicleOwner()
    {
        return $this->belongsTo(VehicleOwner::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function extraServices()
    {
        return $this->belongsToMany(Service::class, 'demand_service');
    }

    public function immatriculation()
    {
        return $this->hasOne(Immatriculation::class);
    }

    public function immatriculation_label()
    {
        return $this->hasOne(ImmatriculationLabel::class);
    }

    public function saleDeclaration()
    {
        return $this->hasOne(SaleDeclaration::class);
    }

    public function plateDuplicate()
    {
        return $this->hasOne(PlateDuplicate::class);
    }

    public function grayCardDuplicate()
    {
        return $this->hasOne(GrayCardDuplicate::class);
    }

    public function demandUpdatesHistories()
    {
        return $this->hasMany(DemandUpdatesHistory::class);
    }

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(VehicleCharacteristic::class, 'characteristic_transformation');
    }

    protected function blackListVerifiedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value  ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function blackListedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }

    protected function isEditable(): Attribute
    {
        return Attribute::make(
            get: fn() => !in_array($this->status, [
                Status::print_order_emitted->name,
                Status::printing_in_progress->name,
                Status::printed->name,
                Status::plate_printed->name,
                Status::closed->name,
            ]) && !in_array($this->service->type->code, [
                AvailableServiceType::SALE_DECLARATION,
                AvailableServiceType::TITLE_DEPOSIT,
                AvailableServiceType::TITLE_RECOVERY,
                AvailableServiceType::TITLE_DEPOSIT_OR_RECOVERY,
                AvailableServiceType::MUTATION,
                AvailableServiceType::RE_IMMATRICULATION,
                AvailableServiceType::TINTED_WINDOW_AUTHORIZATION
            ])
        );
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = generateReference(null, $model->service->code);
        });
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * @throws UnknownServiceException
     */
    public function getCost(): float|int
    {
        return getDemandCost($this);
    }
    /**
     * @throws UnknownServiceException
     */
    public function getStepAttribute(): float|int
    {
        return getDemandStep($this);
    }

    /**
     * @return DemandHandlerService
     * @throws UnknownServiceException
     */
    public function getAdapter(): DemandHandlerService
    {
        return getDemandAdapter($this->service);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withPivot(['amount']);
    }

    public function latestPrintOrder()
    {
        return $this->hasOne(PrintOrder::class)->latest();
    }

    public function order(): Model
    {
        return $this->orders()->where('demand_id', $this->id)->first();
    }

    public function otp()
    {
        return $this->morphOne(DemandOtp::class, 'model')->latest();
    }

    public function initProfileAction(): Model|Builder|null
    {
        return initProfileActionOnDemand($this);
    }

    public function demandActions()
    {
        return $this->hasMany(DemandAction::class);
    }

    public function printOrders()
    {
        return $this->hasMany(PrintOrder::class);
    }

    public function transformation(): HasOne
    {
        return $this->hasOne(VehicleTransformation::class);
    }

    public function getIsDelayedAttribute(): bool
    {
        return $this->status != Status::closed->name && now()->diffInHours($this->submitted_at) > $this->service?->duration;
    }

    public function getDelayedHoursAttribute()
    {
        $duration = now()->diffInHours($this->submitted_at);

        return $duration > $this->service->duration ? $duration - $this->service->duration : $this->service->duration - $duration;
    }

    public function glassEngraving(): HasOne
    {
        return $this->hasOne(GlassEngraving::class);
    }
}
