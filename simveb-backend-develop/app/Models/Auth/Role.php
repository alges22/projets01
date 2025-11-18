<?php

namespace App\Models\Auth;

use App\Traits\SecureDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as ModelsRole;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends ModelsRole implements CanFilterContract
{
    use HasFactory, SecureDelete, Filterable, ActivityTrait, LogsActivity;

    public static function secureDeleteRelations()
    {
        return [
            'users',
        ];
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort()
        ];
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($query)  use ($keyword) {
            $query
                ->where('label', 'like', "%{$keyword}%");
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
