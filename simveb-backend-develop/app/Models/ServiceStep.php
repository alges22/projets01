<?php

namespace App\Models;

use App\Models\Config\Service;
use App\Models\Config\Step;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;

class ServiceStep extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'service_id',
        'step_id',
        'position',
        'duration',
        'process_type'
    ];
    protected $table = 'service_steps';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    static function relations(): array
    {
        return [
            'actions',
            'step',
            'service'
        ];
    }

    public $timestamps = false;

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
