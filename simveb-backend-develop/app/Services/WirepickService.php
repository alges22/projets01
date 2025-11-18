<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WirepickService
{
    public function sendSms($phone, $message)
    {
        $phone = checkPhoneNumber($phone);

        $host = config('config.wirepick.host');
        $user = config('config.wirepick.user');
        $password = config('config.wirepick.password');
        $senderId = config('config.wirepick.senderId');

        $response = Http::get($host . '/httpsms/send', [
            'phone' => $phone,
            'from' => $senderId,
            'text' => $message,
            'client' => $user,
            'password' => $password,
        ]);

        return $response->body();
    }
}
