<?php

namespace App\Models\Config;

use App\Consts\Utils;
use App\Models\Auth\Profile;
use App\Models\Vehicle\Vehicle;
use App\Traits\HasStatusLabel;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class BlacklistVehicle extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, HasStatusLabel, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'author_id',
        'validator_id',
        'validated_at',
        'rejected_at',
        'rejector_id',
        'status',
        'vin'
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected $guarded = [];


    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new Sort(),
            new ScopeFilter('search'),
            new RelativeFilter('status'),
        ];
    }

    /**
     *
     */
    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('status', 'like', "%$keyword%")
                ->orWhereRelation('vehicle', 'vin', 'like', "%$keyword%");
        });
    }

    /**
     * Get the vehicle that is blacklisted
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the officer that is blacklisting the vehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'validator_id');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'rejector_id');
    }

    /**
     * @return string
     */
    private function getEntityName(): string
    {
        return "Véhicule sur la liste noire";
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
    static function relations()
    {
        return [
            'vehicle',
            'author',
            'author.identity',
            'validator',
            'validator.identity',
            'rejector',
            'rejector.identity',
        ];
    }

    /**
     * @return array
     */
    static function secureDeleteRelations()
    {
        return [
            //
        ];
    }
    protected function validatedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function rejectedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function deletedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }
}
