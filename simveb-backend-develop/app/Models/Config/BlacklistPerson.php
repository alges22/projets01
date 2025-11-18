<?php

namespace App\Models\Config;

use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BlacklistPerson extends Model
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'ifu',
        'npi',
        'plate_number',
        'vin',
        'id_number',
    ];

    protected $table = 'blacklist_persons';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('ifu'),
            new RelativeFilter('npi'),
            new RelativeFilter('plate_number'),
            new RelativeFilter('vin'),
            new RelativeFilter('id_number'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('ifu', 'like', "%$keyword%")
                ->orWhere('npi', 'like', "%$keyword%")
                ->orWhere('plate_number', 'like', "%$keyword%")
                ->orWhere('vin', 'like', "%$keyword%")
                ->orWhere('id_number', 'like', "%$keyword%");
        });
    }

    private function getEntityName() : string
    {
        return "Personne sur la liste noire";
    }

    static function relations()
    {
        return [
            //
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            //
        ];
    }
}
