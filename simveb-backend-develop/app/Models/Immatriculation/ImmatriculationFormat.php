<?php

namespace App\Models\Immatriculation;

use App\Models\Vehicle\VehicleCategory;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Database\Factories\Immatriculation\ImmatriculationFormatFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImmatriculationFormat extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable, ActivityTrait, SecureDelete;

    protected $fillable = [
        'vehicle_category_id',
        'format',
        'profile_type_id',
    ];

    protected $casts = ['format' => 'array'];
    protected $appends = ['formatLabel'];

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
            $query->where('format', 'like', "%$keyword%")
                ->orWhereRelation('vehicleCategory', 'name', 'like', "%$keyword%");
        });
    }

    public function getEntityName(): string
    {
        return "Format d'immatriculation";
    }

    public static function relations()
    {
        return [
            'vehicleCategory:id,name',
            'components:id,description'
        ];
    }

    public static function secureDeleteRelations()
    {
        return [
            'immatriculations'
        ];
    }

    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class);
    }

    public function immatriculations()
    {
        return $this->hasMany(Immatriculation::class);
    }

    public function getFormatLabelAttribute()
    {
        $format = '';

        foreach ($this->format as $value) {
            $component = $this->components()
                ->where('code', $value)
                ->first();
            $label = !empty($component->pivot->value) ? $component->pivot->value :  $component?->description;
            $format .= '{' . $label . '}';
        }

        return $format;
    }

    public function components()
    {
        return $this
            ->belongsToMany(FormatComponent::class, 'immatriculation_components')
            ->withPivot(['length', 'position', 'value'])
            ->orderByPivot('position');
    }

    protected static function newFactory(): Factory
    {
        return ImmatriculationFormatFactory::new();
    }
}
