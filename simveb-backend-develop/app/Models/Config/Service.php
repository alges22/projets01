<?php

namespace App\Models\Config;

use App\Consts\Utils;
use App\Interfaces\ModelHasRelations;
use App\Models\Account\User;
use App\Models\Order\Demand;
use App\Models\ServicePriceVariation;
use App\Models\ServiceStep;
use App\Models\Vehicle\VehicleAdministrativeStatus;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Database\Factories\Config\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\RequiredDocumentPackage\Models\DocumentType;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\Factory;

class Service extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory, Filterable, HasUuids, SecureDelete, SoftDeletes, HasRequiredDocumentTypes, HasPermissions;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type_id',
        'duration',
        'cost',
        'procedures',
        'extract',
        'who_can_apply',
        'link',
        'status',
        'published',
        'published_at',
        'published_by',
        'target_organization_id',
        'parent_service_id',
        'vehicle_category_id',
        'image',
        'color',
        'is_child',
        'is_active',
        'can_be_demanded',
        'deactived_at',
        'deactived_by'
    ];

    protected $casts = [
        'is_child' => 'boolean',
        'is_active' => 'boolean',
        'can_be_demanded' => 'boolean',
        'published_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'image' => 'array',
        'vehicle_category_id' => 'array'
    ];

    protected $appends = ['image_url'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->code) {
                $model->code = Service::getUniqueCode();
            }
        });
    }

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
        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%")
            ;
        });
    }

    private function getEntityName() : string
    {
        return 'Service offert par l\'ANATT';
    }

    static function relations(): array
    {
        return [
            'vehicleCategories',
            'vehicleOwnerTypes',
            'serviceExtraServices',
            'type',
            'serviceVehicleCategories',
            'organization',
            'managementCenters',
            'steps',
            'documents',
            'children:id,name,description,image',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
            'children',
            'demands',
        ];
    }


    public function serviceExtraServices()
    {
        return $this->belongsToMany(Service::class,'extra_services', 'service_id', 'extra_service_id');
    }

    public function servicePermissions()
    {
        return $this->belongsToMany(Permission::class,'permission_services')->withPivot('id');
    }

    public function parent()
    {
        return $this->belongsTo(Service::class,'parent_service_id');
    }

    public function children()
    {
        return $this->belongsToMany(Service::class,'service_children','service_id','parent_service_id');
    }

    public function type()
    {
        return $this->belongsTo(ServiceType::class,'type_id');
    }

    public function serviceVehicleCategories()
    {
        return $this->belongsToMany(VehicleCategory::class, 'service_vehicle_categories');
    }

    public function managementCenters()
    {
        return $this->belongsToMany(ManagementCenter::class,'service_management_center');
    }

    public function vehicleAdministrativeStatus()
    {
        return $this->hasMany(VehicleAdministrativeStatus::class);
    }

    static function getUniqueCode()
    {
        $code = generateReference('AS-');

        return self::where('code', $code)->exists() ? self::getUniqueCode() : $code;
    }

    public function publisher()
    {
        return $this->belongsTo(User::class,'published_by');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class,'target_organization_id');
    }

    public function steps()
    {
        return $this->belongsToMany(Step::class, 'service_steps')
            ->withPivot('id','position', 'duration','process_type')->orderByPivot('position');
    }

    public function serviceSteps()
    {
        return $this->hasMany(ServiceStep::class);
    }

    public function documents()
    {
        return $this->belongsToMany(DocumentType::class, 'service_documents')->withPivot('is_required');
    }

    public function getImageUrlAttribute()
    {
        return isset($this->image) ? asset($this->image['path']) : null;
    }

    public function vehicleCategories(): MorphToMany
    {
        return $this->morphedByMany(VehicleCategory::class, 'model', 'service_price_variations')->withPivot('price');
    }

    public function vehicleOwnerTypes(): MorphToMany
    {
        return $this->morphedByMany(OwnerType::class, 'model', 'service_price_variations')->withPivot('price');
    }

    public function vehicleCharacteristics(): MorphToMany
    {
        return $this->morphedByMany(VehicleCharacteristic::class, 'model', 'service_price_variations')->withPivot('price');
    }
    protected function deactivedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function publishedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }

    public function servicePriceVariations()
    {
        return $this->hasMany(ServicePriceVariation::class);
    }

    public function getStep(string $status)
    {
        return $this->steps()->where('status', $status)->first();
    }

    /**
     * Get all of the demands for the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }

    public static function newFactory(): Factory
    {
        return ServiceFactory::new();
    }
}
