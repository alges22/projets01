<?php

namespace App\Services;

use App\Jobs\SendSmsJob;

class SmsService
{
    public function send(string $phone, string $message)
    {
        if ($phone && $message) {
            SendSmsJob::dispatch($phone, $message);
        }
    }
}
