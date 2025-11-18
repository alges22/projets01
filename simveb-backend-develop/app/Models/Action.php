<?php

namespace App\Models;

use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;

class Action extends Model
{
    use HasFactory, HasUuids, Filterable, SecureDelete;

    protected $fillable = [
        'service_step_id',
        'permission_service_id',
        'position',
        'duration',
        'process_type',
        'author_id',
        'pre_status',
        'post_status',
        'description'
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
            new RelativeFilter('position'),
            new RelativeFilter('duration'),
            new ExactFilter('service_step_id'),
            new ExactFilter('permission_service_id'),
            new ExactFilter('process_type'),
            new ExactFilter('author_id')
        ];
    }


    static function relations(): array
    {
        return [
            'permissionService',
            'permissionService.permission',
            'serviceStep'
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
        ];
    }


    /**
     * Get all the demands for the Vehicle
     *
     * @return BelongsTo
     */
    public function permissionService(): BelongsTo
    {
        return $this->belongsTo(PermissionService::class);
    }
    public function permission(): BelongsTo
    {
        return $this->belongsTo(PermissionService::class,'permission_service_id');
    }

    public function serviceStep(): BelongsTo
    {
        return $this->belongsTo(ServiceStep::class);
    }
}
