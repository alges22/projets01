<?php

namespace Ntech\UserPackage\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InactivityReactivationHistory extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'action'
    ];

    const DEACTIVATION = "deactivation";
    const ACTIVATION = "activation";

}
