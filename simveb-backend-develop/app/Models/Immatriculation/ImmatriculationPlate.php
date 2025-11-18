<?php

namespace App\Models\Immatriculation;

use App\Interfaces\ModelHasRelations;
use App\Models\Plate;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ImmatriculationPlate extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
    HasUuids,
    Filterable,
    ActivityTrait,
    LogsActivity,
    SecureDelete,
    SoftDeletes;

    protected $fillable = [
        'plate_id',
        'immatriculation_id',
        'is_lost',
        'is_spoiled',
        'comment',
        'is_active',
        'deactivated_at',
        'deactivation_reason'
    ];

    protected $guarded = [];

    protected $casts = [
        'is_lost' => 'boolean',
        'is_spoiled' => 'boolean',
        'is_active' => 'boolean',
        'deactivated_at' => 'datetime'
    ];

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
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('label', 'like', "%$keyword%")
                ->orWhere('immatriculation_id', 'like', "%$keyword%");
        });
    }

    static function relations(): array
    {
        return [
            'immatriculation',
            'prestigeLabelImmatriculation',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [

        ];
    }

    public function immatriculation()
    {
        return $this->belongsTo(Immatriculation::class);
    }

    public function plate()
    {
        return $this->belongsTo(Plate::class);
    }
}
