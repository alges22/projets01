<?php

namespace App\Models;

use App\Models\Auth\Permission;
use App\Models\Config\Service;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;

class PermissionService extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'service_id',
        'permission_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }


    static function relations(): array
    {
        return [
            'ServiceSteps',
            'permission',
            'service'
        ];
    }

    public function ServiceSteps()
    {
        return $this->belongsToMany(ServiceStep::class,'actions');
    }
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
