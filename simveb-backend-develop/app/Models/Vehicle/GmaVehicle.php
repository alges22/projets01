<?php

namespace App\Models\Vehicle;

use App\Consts\Utils;
use App\Interfaces\ModelHasRelations;
use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class GmaVehicle extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory, HasUuids, Filterable, SecureDelete, HasFiles, SoftDeletes, HasStatusLabel;

    protected $fillable = [
        'customs_reference',
        'vin',
        'status',
        'author_id',
        'vehicle_id',
        'institution_id',
        'validated_at',
        'validated_by',
        'rejected_at',
        'rejected_by',
        'rejected_reason'
    ];

    static function relations(): array
    {
        return [
            'author',
            'author.identity',
            'validator',
            'validator.identity',
            'rejector',
            'rejector.identity',
            'vehicle',
            'institution',
            'institution.town',
            'institution.district',
            'institution.village',
            'institution.type',
            'file'
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    /**
     * Get all the demands for the Vehicle
     *
     * @return BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'validated_by');
    }
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'rejected_by');
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort,
            new RelativeFilter('customs_reference'),
            new RelativeFilter('vin'),
            new ExactFilter('status'),
            new ExactFilter('author_id'),
            new ExactFilter('institution_id'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('customs_reference', 'like', "%$keyword%")
                ->orWhere('vin', 'like', "%$keyword%");
        });
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
