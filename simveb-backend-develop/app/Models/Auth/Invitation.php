<?php

namespace App\Models\Auth;

use App\Models\Space\Space;
use App\Traits\HasStatusLabel;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Invitation extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, HasRoles, HasStatusLabel, Filterable;

    protected $guard_name = 'api';

    protected $fillable = [
        'npi',
        'space_id',
        'profile_type_id',
        'accepted',
        'denied',
        'author_id',
        'status',
        'email',
        'telephone',
    ];

    protected $casts = [
        'denied' => 'bool',
        'accepted' => 'bool'
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function profileType()
    {
        return $this->belongsTo(ProfileType::class);
    }

    public function author()
    {
        return $this->belongsTo(Profile::class, 'author_id');
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
        return $query->where(function ($query) use ($keyword) {
            return $query->where('npi', 'like', "%$keyword%")
                ->orWhere('email', 'like', "%$keyword%")
                ->orWhere('telephone', 'like', "%$keyword%")
            ;
        });
    }
}
