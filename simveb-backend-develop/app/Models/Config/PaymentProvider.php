<?php

namespace App\Models\Config;

use App\Models\Account\User;
use App\Models\Auth\Profile;
use App\Models\Order\Transaction;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PaymentProvider extends Model implements CanFilterContract
{
    use HasFactory,
    HasUuids,
    ActivityTrait,
    LogsActivity,
    SecureDelete,
    Filterable,
    SoftDeletes;

    protected $fillable = [
        'name',
        'is_default',
        'is_active',
        'activator_id',
        'deactivator_id',
        'author_id',
        'activated_at',
        'deactivated_at',
        'telephone',
        'description',
        'code',
        'email'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime'
    ];

    protected $guarded=[];

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
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('name', 'like', "%$keyword%");
        });
    }

    private function getEntityName() : string
    {
        return 'Moyen de paiment';
    }

    public static function relations()
    {
        return [
           'activator',
           'deactivator',
           'author',
           'transactions',
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            'transactions'
        ];
    }

    public function activator()
    {
        return $this->belongsTo(Profile::class, 'activator_id');
    }

    public function deactivator()
    {
        return $this->belongsTo(Profile::class, 'deactivator_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

}
