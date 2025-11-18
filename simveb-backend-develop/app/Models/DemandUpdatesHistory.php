<?php

namespace App\Models;

use App\Models\Order\Demand;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandUpdatesHistory extends Model
{
    use HasFactory, HasUuids, Filterable, SoftDeletes;

    protected $fillable = [
        'old_value',
        'new_value',
        'type',
        'demand_id',
        'is_validated',
        'validated_by',
        'validated_at',
        'model_type',
        'model_id',
    ];

    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'model_type', 'model_id');
    }
}
