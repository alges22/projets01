<?php

namespace App\Models\Vehicle;

use App\Consts\Utils;
use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\Sort;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;

class GmdVehicle extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        HasFiles,
        HasStatusLabel,
        ActivityTrait,
        Filterable,
        SoftDeletes,
        SecureDelete;

    protected $fillable = [
        'vin',
        'customs_reference',
        'institution_id',
        'vehicle_id',
        'status',
        'author_id',
        'validated_by',
        'authored_at',
        'validated_at',
        'rejected_at',
        'rejected_by',
        'rejected_reason'
    ];

    public function getFilters(): array
    {
        return [
            new Sort,
            new RelativeFilter('customs_reference'),
            new RelativeFilter('vin'),
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('customs_reference', 'like', "%$keyword%")
                ->orWhere('vin', 'like', "%$keyword%");
        });
    }

    /**
     * Get the associated vehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the institution that owns the GmdVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the profile that authored the GmdVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    /**
     * Get the profile that validate the GmdVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'validated_by');
    }

    /**
     * @return string
     */
    private function getEntityName(): string
    {
        return "Véhicule du garage matériel de la diplomatie";
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
    static function relations(): array
    {
        return [
            'vehicle.characteristics',
            'author.identity',
            'validator.identity',
            'institution',
            'institution.type',
            'institution.town',
            'institution.district',
            'institution.village',
            'file'
        ];
    }

    /**
     * @return array
     */
    static function secureDeleteRelations(): array
    {
        return [];
    }

    protected function updatedAt(): Attribute
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
