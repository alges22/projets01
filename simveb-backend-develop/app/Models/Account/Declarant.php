<?php

namespace App\Models\Account;

use App\Models\Institution\Institution;
use App\Models\Vehicle\VehicleAdministrativeStatus;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Ntech\UserPackage\Models\Identity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Declarant extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, Notifiable, Filterable, SecureDelete;

    protected $fillable = [
        'identity_id',
        'institution_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Déclarant";
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->whereRelation('identity.firstname', 'like', "%$keyword%");
        });
    }

    public function getFilters()
    {
        return [
            new Sort,
            new ScopeFilter('search'),
        ];
    }

    static function relations(): array
    {
        return [
            'identity:id,firstname,lastname,email,telephone',
            'identity.user:id,identity_id,email',
            'institution',
            'institution.type:id,name,description',
            'institution.town',
            'institution.village',
            'institution.district',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function identity()
    {
        return $this->belongsTo(Identity::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function vehiculeAdministrativeStatus()
    {
        return $this->hasMany(VehicleAdministrativeStatus::class);
    }
}
