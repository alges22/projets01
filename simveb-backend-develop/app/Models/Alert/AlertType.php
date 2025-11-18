<?php

namespace App\Models\Alert;

use App\Traits\SecureDelete;
use Spatie\Activitylog\LogOptions;
use App\Models\Vehicle\VehicleAlert;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Baro\PipelineQueryCollection\ScopeFilter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Baro\PipelineQueryCollection\RelativeFilter;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;

class AlertType extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, Filterable, SecureDelete, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    /*protected $casts = [
        'image' => 'array',
    ];*/

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'code',
    ];

    /**
     *
     *
     * @return string
     */
    private function getEntityName(): string
    {
        return "Type d'alerte";
    }

    /**
     *
     *
     *@return Spatie\Activitylog\LogOptions;
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     *
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('name'),
            new RelativeFilter('description'),
        ];
    }

    /**
     *
     *
     */
    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        });
    }

    /**
     *
     *@return array
     */
    static function secureDeleteRelations(): array {

        return [
            //
        ];
    }

    /**
     * The vehicleAlerts that belong to the AlertType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vehicleAlerts(): BelongsToMany
    {
        return $this->belongsToMany(VehicleAlert::class, 'alert_type_vehicle_alert', 'alert_type_id', 'vehicle_alert_id');
    }
}
