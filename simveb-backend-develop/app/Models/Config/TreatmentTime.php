<?php

namespace App\Models\Config;

use App\Models\Auth\Profile;
use App\Models\Treatment\Treatment;
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

class TreatmentTime extends Model
{
    use HasFactory,
    HasUuids,
    ActivityTrait,
    LogsActivity,
    SecureDelete;

    protected $fillable = [
        'start_at',
        'end_at',
        'profile_id',
        'treatment_id',
        'status'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    //protected $appends = ['duration'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new RelativeFilter('status'),
            new ExactFilter('treatment_id'),
            new ExactFilter('profile_id')
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('status', 'like', "%$keyword%")
            ;
        });
    }

    private function getEntityName() : string
    {
        return "Temps de traitement";
    }

    public static function relations()
    {
        return [
            'profile',
            'treatment'

        ];
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}
