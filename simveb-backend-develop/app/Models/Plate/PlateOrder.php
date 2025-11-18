<?php

namespace App\Models\Plate;

use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Traits\HasInvoices;
use App\Traits\HasStatusLabel;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\DateFromFilter;
use Baro\PipelineQueryCollection\DateToFilter;
use Baro\PipelineQueryCollection\ExactFilter;
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

class PlateOrder extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable, ActivityTrait, LogsActivity, HasStatusLabel, SoftDeletes, HasInvoices;

    protected $fillable = [
        'seller_id',
        'buyer_id',
        'quantity',
        'plate_shape_id',
        'plate_color_id',
        'status',
        'validated_at',
        'validator_id',
        'delivered_at',
        'rejected_at',
        'rejector_id',
        'rejected_reason',
        'validation_file',
        'payment_status',
        'amount',
        'order_data',
        'reference',
        'paid_at',
        'validation_data',
        'author_profile_id',
        'delivery_slip_file',
        'confirmator_id',
        'confirmed_at'
    ];

    protected $casts = [
        'order_data' => 'array',
        'validation_data' => 'array',
        'validated_at' => 'datetime',
        'delivered_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    protected $hidden = [
        'validation_file',
    ];

    static function relations()
    {
        return [
            'authorProfile:id,identity_id',
            'authorProfile.identity:id,firstname,lastname',
            'buyer:id,name,telephone,ifu,email',
            'buyer.type:id,name',
            'seller:id,name,telephone,ifu,email',
            'seller.type:id,name',
            'validator:id,identity_id',
            'validator.identity:id,firstname,lastname',
            'rejector:id,identity_id',
            'rejector.identity:id,firstname,lastname',
            'confirmator:id,identity_id',
            'confirmator.identity:id,firstname,lastname',
        ];
    }

    public function getFilters()
    {
        return [
            new Sort,
            new RelativeFilter('status'),
            new ExactFilter('author_profile_id'),
            new ExactFilter('quantity'),
            new ExactFilter('validator_id'),
            new ExactFilter('rejector_id'),
            new ExactFilter('confirmator_id'),
            new ExactFilter('seller_id'),
            new ExactFilter('buyer_id'),
            new DateFromFilter('validated_at'),
            new DateToFilter('validated_at'),
            new DateFromFilter('rejected_at'),
            new DateToFilter('rejected_at'),
            new DateFromFilter('confirmed_at'),
            new DateToFilter('confirmed_at'),
            new ScopeFilter('search'),
        ];
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('reference', 'like', "%$keyword%");
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Commande de plaque";
    }

    public function getOrderDetailsAttribute()
    {
        $orderDetails = [];
        foreach ($this->order_data as $row) {
            $shape = PlateShape::find($row['plate_shape_id']);
            $color = PlateColor::find($row['plate_color_id']);

            $orderDetails[] = [
                'shape' => $shape->name,
                'color' => $color->label,
                'nb' => $row['nb'],
                'unity_amount' => $shape->cost + $color->cost,
                'total_amount' => ($shape->cost + $color->cost) * $row['nb'],
            ];
        }

        return $orderDetails;
    }


    public function validator()
    {
        return $this->belongsTo(Profile::class, 'validator_id');
    }

    public function rejector()
    {
        return $this->belongsTo(Profile::class, 'rejector_id');
    }

    public function confirmator()
    {
        return $this->belongsTo(Profile::class, 'confirmator_id');
    }

    public function authorProfile()
    {
        return $this->belongsTo(Profile::class, 'author_profile_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Institution::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(Institution::class, 'seller_id');
    }
}
