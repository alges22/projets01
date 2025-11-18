<?php

namespace App\Models\Plate;

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
use Spatie\Activitylog\LogOptions;

class PlateShape extends Model implements CanFilterContract
{
    use HasFactory, Filterable, HasUuids, SecureDelete, SoftDeletes;

    protected $fillable = ['name', 'description', 'cost', 'image', 'code'];

    protected $casts = [];

    protected $guarded=[];

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
        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%")
            ;
        });
    }

    private function getEntityName() : string
    {
        return "Forme de plaque";
    }

    public static function relations()
    {
        return [

        ];
    }

    public static function secureDeleteRelations()
    {
        return [

        ];
    }
}
