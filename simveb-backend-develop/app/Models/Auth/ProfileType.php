<?php

namespace App\Models\Auth;

use App\Models\Space\Space;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Models\Plate\PlateColor;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Database\Factories\Auth\ProfileTypeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileType extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable, HasRoles;

    protected $guard_name = 'api';

    protected $fillable = [
        'code',
        'name',
        'icon_path',
        'role_id'
    ];

    protected $cats = [
        'icon_path' => 'array',
    ];

    protected $appends = [
        'icon',
    ];

    public function spaces()
    {
        return $this->hasMany(Space::class, 'profile_type_id');
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class, 'type_id');
    }

    public function plateColors()
    {
        return $this->belongsToMany(PlateColor::class);
    }

    public function getFilters()
    {
        return [
            new RelativeFilter('code'),
            new RelativeFilter('name'),
            new ScopeFilter('search'),
        ];
    }

    static function relations()
    {
        return [
            'plateColors:id,name,label,color_code,text_color,cost',
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($query) use ($keyword) {
            return $query->where('name', 'like', "%$keyword%")
                ->orWhere('code', 'like', "%$keyword%");
        });
    }

    public function immatriculationFormat()
    {
        return $this->hasOne(ImmatriculationFormat::class);
    }

    public function getIconAttribute()
    {
        if (isset($this->attributes['icon_path'])) {
            return asset(json_decode($this->attributes['icon_path'])->path);
        }
        return null;
    }

    /**
     * Get the role that owns this type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public static function newFactory(): Factory
    {
        return ProfileTypeFactory::new();
    }
}
