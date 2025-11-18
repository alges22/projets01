<?php

namespace App\Models\Plate;

use App\Models\Auth\ProfileType;
use App\Models\Config\ImmatriculationType;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Plate;
use App\Models\PlateTransformation;
use App\Models\Reimmatriculation;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
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

class PlateColor extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'name',
        'label',
        'color_code',
        'text_color',
        'cost'
    ];

    protected $casts = [];

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('name'),
            new RelativeFilter('label'),
            new RelativeFilter('color_code'),
            new RelativeFilter('text_color'),
            new ExactFilter('cost'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('label', 'like', "%$keyword%")
                ->orWhere('color_code', 'like', "%$keyword%")
                ->orWhere('text_color', 'like', "%$keyword%");
        });
    }

    private function getEntityName(): string
    {
        return "Couleur de plaque";
    }

    public function immatriculations()
    {
        return $this->hasMany(Immatriculation::class);
    }

    public function plates()
    {
        return $this->hasMany(Plate::class);
    }

    public function plateOrders()
    {
        return $this->hasMany(PlateOrder::class);
    }

    public function profileTypes()
    {
        return $this->belongsToMany(ProfileType::class);
    }

    public function plateTransformations()
    {
        return $this->hasMany(PlateTransformation::class);
    }

    public function reimmatriculations()
    {
        return $this->hasMany(Reimmatriculation::class);
    }

    public function immatriculationTypes()
    {
        return $this->belongsToMany(ImmatriculationType::class, 'immatriculation_type_color', 'plate_color_id', 'immatriculation_type_id');
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
            'plates',
            'immatriculations',
            'plateOrders',
            'profileTypes',
            'plateTransformations',
            'reimmatriculations',
            'immatriculationTypes',
        ];
    }
}
