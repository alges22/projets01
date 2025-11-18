<?php

namespace App\Models\Config;

use App\Models\Opposition;
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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TitleReason extends Model implements CanFilterContract
{
    use HasUuids,
        HasFactory,
        ActivityTrait,
        LogsActivity,
        Filterable,
        SecureDelete,
        SoftDeletes;

    protected $fillable = [
        'label',
        'description',
        'reason_type',
    ];


    /**
     * @return array
     */
    public static function relations(): array
    {
        return [
            'reasonType:id,name,description',
        ];
    }

    /**
     * @return array
     */
    public static function secureDeleteRelations()
    {
        return [
            'reasonType',
        ];
    }

    /**
     * @return string
     */
    private function getEntityName(): string
    {
        return "Motif d'un dépot de titre";
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
            new RelativeFilter('label'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('label', 'like', "%$keyword%");
        });
    }

    public function oppositions(): HasMany
    {
        return $this->hasMany(Opposition::class);
    }

    public function reasonType(): BelongsTo
    {
        return $this->belongsTo(TitleReasonType::class, 'reason_type');
    }
}
