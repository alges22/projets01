<?php

namespace App\Models;

use App\Models\Auth\Profile;
use App\Models\Order\Demand;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandAction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'demand_id',
        'action_id',
        'profile_id',
        'assigned_at',
        'done_at',
        'duration',
        'status',
        'done_status',
    ];

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function demand(): BelongsTo
    {
        return $this->belongsTo(Demand::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
