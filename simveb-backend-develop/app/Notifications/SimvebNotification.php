<?php

namespace App\Notifications;

use App\Models\Config\NotificationConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class SimvebNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $messageSms;
    protected $messageInApp;
    protected $messageMail;
    protected $useSmsChannel;
    protected $useEmailChannel;
    protected array $emailAction;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $name, bool $useEmailChannel, bool $useSmsChannel, array $emailAction = null)
    {
        $notificationConfig = NotificationConfig::where('name', $name)->first();

        if (!empty($notificationConfig)) {
            $this->title = $notificationConfig->title;
            $this->messageSms = $notificationConfig->message_sms;
            $this->messageInApp = $notificationConfig->message_in_app;
            $this->messageMail = $notificationConfig->message_mail;
            $this->useEmailChannel = $useEmailChannel;
            $this->useSmsChannel = $useSmsChannel;
            $this->emailAction = $emailAction;
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->useSmsChannel) {
            $channels[] = 'vonage';
        }

        if ($this->useEmailChannel) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = new MailMessage;
        $mailMessage->subject($this->title)->line($this->messageMail);

        if ($this->emailAction) {
            $mailMessage->action($this->emailAction['text'], $this->emailAction['url']);
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => $this->messageInApp
        ];
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return VonageMessage
     */
    public function toVonage($notifiable)
    {
        return (new VonageMessage())
            ->content($this->messageSms)
            ->unicode();
    }
}
