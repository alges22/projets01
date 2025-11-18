<?php

namespace App\Channels;

use App\Services\SmsService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(private readonly SmsService $service) {}

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);

        $telephone = $notifiable->routeNotificationForSms();

        if ($telephone && $message) {
            $this->service->send($telephone, $message);
        }
    }
}
