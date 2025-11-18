<?php

namespace App\Models;

use App\Traits\HasTransactions;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Wallet extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable, ActivityTrait, LogsActivity, HasTransactions;

    protected $fillable = [
        'model_id',
        'model_type',
        'balance',
    ];

    private function getEntityName() : string
    {
        return "Portefeuille";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new Sort(),
            new ExactFilter('amount'),
            new ExactFilter('model_type'),
            new ExactFilter('model_id'),
        ];
    }

    public function model()
    {
        return $this->morphTo();
    }
}
