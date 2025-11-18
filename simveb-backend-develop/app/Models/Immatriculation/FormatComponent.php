<?php

namespace App\Models\Immatriculation;

use App\Models\Config\Zone;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class FormatComponent extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable;

    protected $fillable = [
        'code','description','default_value','default_length','possible_values','is_auto'
    ];

    const PREFIX = 'prefix';
    const COUNTRY_CODE = 'country_code';
    const NUMERIC_LABEL = 'numeric_label';
    const ALPHABETIC_LABEL = 'alphabetic_label';
    const ZONE = 'zone';

    protected $casts = ['possible_values' => 'array'];

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
            $query->where('code', 'like', "%$keyword%")
                ->orWhere('name', 'like', "%$keyword%");
        });
    }

    public function getEntityName() : string
    {
        return "Format de composant";
    }

    public static function relations()
    {
        return [

        ];
    }

    protected function possibleValues(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->attributes['code'] == self::ZONE ? Zone::query()->pluck('code')->toArray() :  json_decode($value),
        );
    }

    public static function secureDeleteRelations()
    {
        return [

        ];
    }
}
