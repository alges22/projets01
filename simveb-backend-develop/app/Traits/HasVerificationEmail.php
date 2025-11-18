<?php
namespace App\Traits;


use App\Models\Auth\EmailVerification;
use Illuminate\Support\Str;

trait HasVerificationEmail
{
    public function generateVerificationToken()
    {
       return EmailVerification::query()->create([
            'email' => $this->email,
            'expire_at' => now()->addMinutes(60),
            'token' => Str::random(32)
        ])->token;
    }

}
