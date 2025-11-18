<?php

namespace App\Models\Config;

use App\Models\Plate\PlateColor;
use App\Traits\HasStatusLabel;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ImmatriculationType extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        Filterable,
        SecureDelete,
        SoftDeletes;

    protected $fillable = [
        'label',
        'code',
        'description'
    ];

    /**
     * The color's plates related to the Immatriculation Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function plateColors(): BelongsToMany
    {
        return $this->belongsToMany(PlateColor::class, 'immatriculation_type_color', 'immatriculation_type_id', 'plate_color_id');
    }

    /**
     * @return string
     */
    private function getEntityName(): string
    {
        return "Type d'immatriculation";
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
            'plateColors',
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

    public function getFilters()
    {
        return [
            new Sort,
            new ScopeFilter('search'),
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('label', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        });
    }
}
