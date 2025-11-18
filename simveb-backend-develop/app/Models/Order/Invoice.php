<?php

namespace App\Models\Order;

use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Models\Vehicle\VehicleOwner;
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
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        Filterable,
        SoftDeletes;

    protected $fillable = [
        'reference',
        'amount',
        'status',
        'model_id',
        'paid_at',
        'profile_id',
        'institution_id',
        'model_type',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

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
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('reference', 'like', "%$keyword%")
                ->orWhere('status', 'like', "%$keyword%");
        });
    }

    private function getEntityName(): string
    {
        return "Facture";
    }

    public static function relations()
    {
        return [
            'order',
            'owner'
        ];
    }

    public static function secureDeleteRelations()
    {
        return [];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function owner()
    {
        return $this->belongsToMany(VehicleOwner::class);
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
            $model->reference = generateReference("INV");
        });
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
