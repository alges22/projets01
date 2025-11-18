<?php

namespace Ntech\MetadataPackage\Models;

use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaData extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable;

    protected $fillable = ["name", 'label', 'data'];

    protected $casts = [
        "data" => "array"
    ];

    public function getFilters()
    {
        return [
            new Sort,
            new ScopeFilter('search')
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('label', 'like', "%$keyword%")
                ->orWhere('name', 'like', "%$keyword%");
        });
    }
}
