<?php

namespace App\Models\Vehicle;

use App\Interfaces\ModelHasRelations;
use App\Models\Auth\Profile;
use App\Models\Config\LegalStatus;
use App\Models\Config\OwnerType;
use App\Models\GlassEngraving;
use App\Models\Institution\Institution;
use App\Models\Opposition;
use App\Models\Order\Demand;
use App\Models\VehicleTransformation;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Ntech\UserPackage\Models\Identity;

class VehicleOwner extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory, Filterable, HasUuids, SecureDelete, Notifiable;

    protected $fillable = [
        'bfu',
        'name',
        'legal_status',
        'identity_id',
        'institution_id',
        'profile_id',
    ];

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('bfu'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('bfu', 'like', "%$keyword%")
                ->whereRelation('identity', 'npi', 'like', "%$keyword%")
                ->whereRelation('institution', 'ifu', 'like', "%$keyword%");
        });
    }

    static function relations(): array
    {
        return [
            'vehicles',
            'identity',
            'institution',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
            'vehicles',
            'immatriculationDemand'
        ];
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'owner_id');
    }

    public function immatriculationDemand()
    {
        return $this->hasMany(Demand::class);
    }
    public function identity()
    {
        return $this->belongsTo(Identity::class);
    }
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function ownerType()
    {
        return $this->belongsTo(OwnerType::class);
    }
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function legalStatus()
    {
        return $this->belongsTo(LegalStatus::class);
    }

    public function vehicleAdministrativeStatus()
    {
        return $this->hasMany(VehicleAdministrativeStatus::class);
    }

    public function opposition(): HasMany
    {
        return $this->hasMany(Opposition::class);
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
