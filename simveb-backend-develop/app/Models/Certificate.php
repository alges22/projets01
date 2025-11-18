<?php

namespace App\Models;

use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Certificate extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        Filterable,
        SoftDeletes;

    protected $fillable = [
        'template_name',
        'model_type',
        'model_id',
        'profile_id',
        'institution_id',
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
            $query->where('templateName', 'like', "%$keyword%")
                ->orWhere('status', 'like', "%$keyword%");
        });
    }

    private function getEntityName(): string
    {
        return "Certificat";
    }

    public static function relations()
    {
        return [
            'model'
        ];
    }

    public static function secureDeleteRelations()
    {
        return [];
    }

    public function model(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'model_type', 'model_id');
    }


    /**
     * Get all the demands for the Vehicle
     *
     * @return BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
