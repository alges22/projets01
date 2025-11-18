<?php

namespace App\Models\Immatriculation;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastImmatriculation extends Model
{
    use HasFactory,
    HasUuids;

    protected $fillable = ['prefix','alphabetic_label','zone','numeric_label','country_code','vehicle_category_id'];

    protected $casts = [];

    protected $guarded = [];
}
