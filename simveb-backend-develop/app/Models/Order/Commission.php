<?php

namespace App\Models\Order;

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

class Commission extends Model implements CanFilterContract
{
    use HasFactory, HasUuids,  LogsActivity, ActivityTrait, Filterable, SecureDelete, SoftDeletes;


    protected $fillable = [
        'name',
        'amount',
        'percentage',
        'description',
        'created_by',
        'calculation_base',
    ];

    const CALCULATION_BASES = [
        [
            'label' => 'Frais de livraison',
            'value' => 'delivery_fees'
        ],
        [
            'label' => 'Total de la commande',
            'value' => 'amount'
        ],
        [
            'label' => 'Montant total de la commande',
            'value' => 'total_amount'
        ],
    ];

    const DELIVERY_FEES = 'delivery_fees';
    const AMOUNT = 'amount';
    const TOTAL_AMOUNT = 'total_amount';


    protected $casts = [
        'amount' => 'double',
        'percentage' => 'double',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Commission";
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort()
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        });
    }



    public static function secureDeleteRelations()
    {
        return [];
    }
    public static function relations(): array
    {
        return [];
    }
}
