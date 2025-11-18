<?php

namespace App\Models\Order;

use App\Consts\Utils;
use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Services\Treatment\TreatmentService;
use App\Traits\HasInvoices;
use App\Traits\HasTransactions;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\DateFromFilter;
use Baro\PipelineQueryCollection\DateToFilter;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity,
        SecureDelete,
        Filterable,
        HasTransactions,
        SoftDeletes,
        HasInvoices;

    protected $fillable = [
        'reference',
        'amount',
        'status',
        'payment_status',
        'institution_id',
        'profile_id',
        'payment_provider_id',
        'submitted_at',
        'paid_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'paid_at' => 'datetime'
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
            new ScopeFilter('npi'),
            new ScopeFilter('ifu'),
            new Sort(),
            new RelativeFilter('status'),
            new RelativeFilter('payment_status'),
            new ExactFilter('payment_provider_id'),
            new ExactFilter('institution_id'),
            new ExactFilter('profile_id'),
            new DateFromFilter('submitted_at'),
            new DateFromFilter('paid_at'),
            new DateToFilter('submitted_at'),
            new DateToFilter('paid_at'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('reference', 'like', "%$keyword%")
                ->orWhere('amount', 'like', "%$keyword%")
                ->orWhere('status', 'like', "%$keyword%")
                ->orWhere('payment_status', 'like', "%$keyword%");
        });
    }

    public function scopeNpi(Builder $query, string $keyword)
    {

        return $query->withWhereHas('profile', function ($q) use ($keyword) {
            $q->withWhereHas('identity', function ($k) use ($keyword) {
                $k->where('npi', $keyword);
            });
        });
    }

    public function scopeIfu(Builder $query, string $keyword)
    {

        return $query->withWhereHas('institution', function ($q) use ($keyword) {
            $q->where('ifu', $keyword);
        });
    }

    private function getEntityName() : string
    {
        return "Commande";
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
        return [

        ];
    }

    public function demands() : BelongsToMany
    {
        return $this->belongsToMany(Demand::class)->withPivot(['amount']);
    }

    public function submit()
    {
        $treatmentService = new TreatmentService;

        foreach ($this->demands as $demand)
        {
            try {
                $treatmentService->submitDemand($demand->id);
            } catch (ValidationException $e) {
                Log::debug($e);
            }
        }
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
            $model->reference = generateReference("CMD");
        });
    }
    protected function submittedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function paidAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : null
        );
    }
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : $value
        );
    }
    protected function deletedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : $value
        );
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
