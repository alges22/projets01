<?php

namespace App\Models;

use App\Models\Config\TransformationType;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Traits\SecureDelete;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TransformationCharacteristic extends Model
{
    use HasFactory, HasUuids, LogsActivity, SecureDelete, SoftDeletes;

    protected $fillable = [
        'transformation_id',
        'characteristic_id',
        'old_characteristic',
        'new_characteristic',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    static function relations(): array
    {
        return [
            'transformation',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
            'transformation',
        ];
    }

    public function getEntityName() : string
    {
        return "Caractéristiques de transformation";
    }

    public function transformation(): BelongsTo
    {
        return $this->belongsTo(VehicleTransformation::class, 'transformation_id');
    }

    public function oldCharacteristic(): BelongsTo
    {
        return $this->belongsTo(VehicleCharacteristic::class, 'old_characteristic');
    }

    public function newCharacteristic(): BelongsTo
    {
        return $this->belongsTo(VehicleCharacteristic::class, 'new_characteristic');
    }
}
