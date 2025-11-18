<?php

namespace App\Models\Config;

use App\Interfaces\ModelHasRelations;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalStatus extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
        Filterable,
        HasUuids;

    protected $fillable = ['name', 'description'];

    protected $casts = [];

    protected $guarded = [];

    static function relations(): array
    {
        return [];
    }

    static function secureDeleteRelations(): array
    {
        return [];
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
        $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        });
    }
}
