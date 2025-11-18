<?php

namespace App\Notifications;

use App\Exceptions\NotificationConfigNotFoundException;
use App\Models\Config\NotificationConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class NotificationSender extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;

    protected array $emailAction;
    private $notificationConfig;
    private $messageSms;
    private $messageInApp;
    private $messageMail;
    private $link = null;
    protected $attachment;
    protected $file;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $name, private array $channels = ['mail'], private $data = null)
    {
        $this->notificationConfig = NotificationConfig::where('name', $name)->first();
        if (!$this->notificationConfig) {
            throw new NotificationConfigNotFoundException();
        }

        if (isset($this->data['link'])) {
            $this->link = Arr::pull($this->data, 'link');
        }

        if (isset($this->data['attachment'])) {
            $this->attachment = Arr::pull($this->data, 'attachment');
        }

        if (isset($this->data['file'])) {
            $this->file = Arr::pull($this->data, 'file');
        }

        if (!empty($this->notificationConfig)) {
            $this->title = $this->notificationConfig->title;
            $this->messageSms = $this->getNotificationMessage($this->notificationConfig->message_sms);
            $this->messageInApp = $this->getNotificationMessage($this->notificationConfig->message_in_app);
            $this->messageMail = $this->getNotificationMessage($this->notificationConfig->message_mail);
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $payload = ['customVariables' => 'Hello'];
        if (isset($this->attachments)) {
            $payload['attachment'] = $this->attachment;
        }

        $mailMessage = new MailMessage;
        $mailMessage->subject($this->title)->line($this->messageMail);

        if (isset($this->link)) {
            $mailMessage->action($this->link['text'], $this->link['url']);
        }

        if (isset($this->attachment)) {
            $entityName = strtolower(Str::slug($this->data['entityName'] ?? 'document'));
            $filename = $entityName . '_' . now()->timestamp . '_' . Str::random(3) . '.pdf';

            $mailMessage->attach($this->attachment, [
                'as' => $filename,
            ]);
        }

        if (isset($this->file)) {
            $mailMessage->attachData($this->file, 'name.pdf');
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

    public function getNotificationMessage($message)
    {

        if ($this->data) {
            foreach ($this->data as $key => $value) {
                $message = Str::replace('{' . $key . '}', $value, $message);
            }
        }

        return $message;
    }

    public function toSms($notifiable)
    {
        return $this->messageSms;
    }
}
