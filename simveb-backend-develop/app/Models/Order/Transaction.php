<?php

namespace App\Models\Order;

use App\Models\Config\PaymentProvider;
use App\Traits\HasStatusLabel;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\DateFromFilter;
use Baro\PipelineQueryCollection\DateToFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, HasStatusLabel;

    protected $fillable = [
        'reference',
        'payment_reference',
        'amount',
        'fees',
        'total_amount',
        'status',
        'payment_provider_id',
        'model_id',
        'model_type',
        'type',
    ];

    protected $casts = [];

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
            new DateFromFilter('created_at'),
            new DateToFilter('created_at'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('reference', 'like', "%$keyword%")
                ->orWhere('payment_id', 'like', "%$keyword%");
        });
    }

    private function getEntityName(): string
    {
        return "Transaction";
    }


    static function relations()
    {
        return [];
    }

    public function paymentProvider()
    {
        return $this->belongsTo(PaymentProvider::class);
    }


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = generateReference("TX-");
        });
    }
}
