<?php

namespace App\Models;

use App\Consts\Utils;
use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Models\Vehicle\Vehicle;
use App\Traits\HasFiles;
use App\Traits\HasOtpCodes;
use App\Traits\HasTreatments;
use App\Traits\SecureDelete;
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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Motorcycle extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        Filterable,
        HasTreatments,
        HasFiles,
        HasOtpCodes,
        SecureDelete,
        SoftDeletes;

    protected $fillable = [
        "customs_reference",
        "vin",
        "npi",
        "vehicle_id",
        "buyer_id",
        "institution_id",
        "author_id",
    ];

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('customs_reference'),
            new ExactFilter('author_id'),
            new RelativeFilter('vin'),
            new RelativeFilter('npi'),
            new DateFromFilter('created_at'),
            new DateToFilter('created_at'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('customs_reference', 'like', "%$keyword%")
                ->orWhere('vin', 'like', "%$keyword%")
                ->orWhere('npi', 'like', "%$keyword%");
        });
    }

    static function relations(): array
    {
        return [
            "vehicle",
            "institution",
            "institution.type",
            "institution.town",
            "institution.district",
            "institution.village",
            "buyer",
            "buyer.identity",
            "author",
            "author.identity",
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Profile::class, 'buyer_id');
    }

    public function author()
    {
        return $this->belongsTo(Profile::class, 'author_id');
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
