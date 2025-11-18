<?php

namespace App\Models\Vehicle;

use App\Consts\Utils;
use App\Models\Auth\Profile;
use App\Traits\SecureDelete;
use App\Traits\HasStatusLabel;
use App\Models\Alert\AlertType;
use App\Interfaces\ModelHasRelations;
use App\Models\Config\Border;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VehicleAlert extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        Filterable,
        HasStatusLabel,
        SecureDelete,
        SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'status',
        'driver_firstname',
        'driver_lastname',
        'comment',
        'author_id',
        'cancelor_id',
        'authored_at',
        'canceled_at',
        'reference',
        'border_id',
    ];

    /**
     *
     */
    protected $casts = [
        'authored_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    private function getEntityName(): string
    {
        return "Catégorie de véhicule";
    }


    static function relations(): array
    {
        return [
            'officer',
            'vehicle',
            'border'
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    /**
     * Get the vehicle concern by the alert
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the officer that create the alert
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    /**
     * Get the border that owns the VehicleAlert
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function border(): BelongsTo
    {
        return $this->belongsTo(Border::class);
    }

    /**
     * The types that belong to the VehicleAlert
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function alertTypes(): BelongsToMany
    {
        return $this->belongsToMany(AlertType::class, 'alert_type_vehicle_alert', 'vehicle_alert_id', 'alert_type_id');
    }

    /**
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('driver_firstname'),
            new RelativeFilter('driver_lastname'),
        ];
    }

    /**
     *
     */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }

    /**
     *
     */
    protected function recordedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = generateReference("ALRT");
        });
    }
}
