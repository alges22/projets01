<?php

namespace Ntech\UserPackage\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordReset
 * @package App
 */
class PasswordReset extends Model
{

    protected $fillable = [
        'email', 'token'
    ];
}
