<?php

namespace Ntech\UserPackage\Models;

use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;

class Module extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable;

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
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
            $q->where('name', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%")
                ->orWherehas('permissions', function ($q) use ($keyword) {
                    $q->where('label', 'like', "%$keyword%");
                });
        });
    }
}
