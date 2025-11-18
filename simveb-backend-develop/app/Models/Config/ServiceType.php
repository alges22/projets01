<?php

namespace App\Models\Config;

use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Database\Factories\Config\ServiceTypeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class ServiceType extends Model implements CanFilterContract
{
    use HasFactory, Filterable, HasUuids, SecureDelete, SoftDeletes;

    protected $fillable = ['code', 'name', 'description', 'cost'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->code) {
                $model->code = ServiceType::getUniqueCode();
            }
        });
    }

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
        return 'Type de service';
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

    static function getUniqueCode()
    {
        $code = generateReference('AST-');

        return self::where('code', $code)->exists() ? self::getUniqueCode() : $code;
    }

    public static function newFactory(): Factory
    {
        return ServiceTypeFactory::new();
    }
}
