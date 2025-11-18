<?php

namespace Ntech\UserPackage\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordReset
 * @package App
 */
class PasswordResetToken extends Model
{
    use HasUuids;

    protected $fillable = [
        'email', 'token'
    ];
}
