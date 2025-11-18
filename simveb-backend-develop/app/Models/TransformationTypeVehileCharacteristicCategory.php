<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransformationTypeVehileCharacteristicCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'characteristic_id',
    ];
}
