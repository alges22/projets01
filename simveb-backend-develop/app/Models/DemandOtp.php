<?php

namespace App\Models;

use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandOtp extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable;

    protected $fillable = [
        'owner_otp',
        'buyer_otp',
        'is_verified',
        'verified_at',
        'model_type',
        'model_id',
        'owner_npi',
        'buyer_npi',
        'owner_ifu',
        'buyer_ifu',
        'expire_at'
    ];

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
            return $query->where('owner_otp', 'like', "%$keyword%")
                ->orWhere('buyer_otp', 'like', "%$keyword%");
        });
    }
}
