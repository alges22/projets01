<?php

namespace Ntech\UserPackage\Models;


use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasUuids;

    protected $fillable = ['email',"ip","attempts","last_attempt_at"];

    const UPDATED_AT = null;
}
