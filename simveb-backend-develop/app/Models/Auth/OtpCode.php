<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtpCode extends Model
{
    use HasFactory,
    HasUuids,
    SoftDeletes;

    protected $fillable = [
        'value',
        'expired_at',
        'model',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    protected $guarded=[];

    public function model()
    {
        return $this->morphTo();
    }
}
