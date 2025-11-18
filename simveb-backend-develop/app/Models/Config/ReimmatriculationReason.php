<?php

namespace App\Models\Config;

use Baro\PipelineQueryCollection\BooleanFilter;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ReimmatriculationReason extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, Filterable, SoftDeletes;

    protected $fillable = [
        'title',
        'code',
        'requires_reason',
        'requires_title_deposit',
        'requires_transfer_certificate',
        'enable_plate_transformation',
        'img_path',
    ];

    protected $appends = [
        'img_url',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new Sort(),
            new RelativeFilter('title'),
            new RelativeFilter('code'),
            new BooleanFilter('requires_reason'),
            new BooleanFilter('requires_tite_deposit'),
            new BooleanFilter('requires_tite_certificate'),
            new ScopeFilter('search'),
        ];
    }

    private function getEntityName(): string
    {
        return "Motif de ré-immatriculation";
    }

    public function getImgUrlAttribute()
    {
        return $this->img_path ? asset($this->img_path) : null;
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%$keyword%")
                ->orWhere('code', 'like', "%$keyword%");
        });
    }
}
