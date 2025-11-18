<?php

namespace App\Models\Config;

use App\Models\Account\User;
use App\Models\Vehicle\Vehicle;
use App\Traits\HasFiles;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;
use Spatie\Activitylog\LogOptions;

class IntlVehicleRegDoc extends Model implements CanFilterContract
{
    use HasFactory,
        Filterable,
        HasUuids,
        SecureDelete,
        SoftDeletes,
        HasRequiredDocumentTypes,
        HasFiles;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'under_review',
        'approved',
        'paid',
        'ongoing_issuance',
        'issued',
        'expired',
        'reviewed_at',
        'approved_at',
        'paid_at',
        'issued_at',
        'expired_at',
        'validity_period_in_months'
    ];

    protected $guarded = [
        'reviewed_at',
        'approved_at',
        'paid_at',
        'issued_at',
        'expired_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'issued_at' => 'datetime',
        'expired_at' => 'datetime'
    ];

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
            $query->whereHas('user', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%")
                    ->orWhere('username', 'like', "%$keyword%")
                    ->orWhere('email', 'like', "%$keyword%");
            });
        });
    }

    private function getEntityName(): string
    {
        return "Carte grise internationale";
    }

    public static function relations()
    {
        return [
            'user',
            'vehicle',
            'files',
        ];
    }

    public static function secureDeleteRelations()
    {
        return [
            'files',
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
