<?php

namespace Ntech\NotifierPackage;

use App\Exceptions\NotificationConfigNotFoundException;
use App\Models\Config\NotificationConfig;
use Illuminate\Support\Arr;
use Novu\Laravel\Facades\Novu;
use Ntech\NotifierPackage\Contracts\NotifierContract;
use Ntech\NotifierPackage\Enums\NotifierWorkflowTypeEnum;
use Ntech\NotifierPackage\Exceptions\NotifierChannelNotFoundException;
use Illuminate\Support\Str;

class Notifier implements NotifierContract
{
    protected $notificationConfig;

    protected string $tenantIdentifier;

    public function __construct()
    {
        $this->tenantIdentifier = config('novu.tenant_identifier', Str::slug('-', config('app.name')));
    }

    /**
     * Process notification to specified channel
     *
     * @param string $channel
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     *
     * @throws \App\Exceptions\NotificationConfigNotFoundException | \Ntech\NotifierPackage\Exceptions\NotifierChannelNotFoundException
     */
    public function process(string $channel, string $notifName, string|array $subscribersId, array $data)
    {
        $this->notificationConfig = NotificationConfig::where('name', $notifName)->first();
        if (!$this->notificationConfig) {
            throw new NotificationConfigNotFoundException;
        }

        if ($channel == NotifierWorkflowTypeEnum::in_app->name) {
            $this->processInApp($notifName, $subscribersId, $data);
        } elseif ($channel == NotifierWorkflowTypeEnum::email->name) {
            $this->processEmail($notifName, $subscribersId, $data);
        } elseif ($channel == NotifierWorkflowTypeEnum::sms->name) {
            $this->processSms($notifName, $subscribersId, $data);
        } elseif ($channel == NotifierWorkflowTypeEnum::chat->name) {
            $this->processChat($notifName, $subscribersId, $data);
        } elseif ($channel == NotifierWorkflowTypeEnum::push->name) {
            $this->processPush($notifName, $subscribersId, $data);
        } else {
            throw new NotifierChannelNotFoundException($channel);
        }
    }

    /**
     * Process notification in app
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processInApp(string $notifName, string|array $subscribersId, array $data)
    {
        return $this->triggerWorkflow(NotifierWorkflowTypeEnum::in_app->name, $this->formatSubscribers($subscribersId), $data);
    }

    /**
     * Process email notification
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processEmail(string $notifName, string|array $subscribersId, array $data)
    {
        $payload = [];
        if (isset($data['link'])) {
            $link = Arr::pull($data, 'link');
            $payload = [
                'has_button' => '1',
                'custom_button_text' => $link['text'],
                'custom_button_link' => $link['url'],
            ];
        }

        return $this->triggerWorkflow(NotifierWorkflowTypeEnum::email->name, $this->formatSubscribers($subscribersId), $data, $payload);
    }

    /**
     * Process sms notification
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processSms(string $notifName, string|array $subscribersId, array $data)
    {
        return $this->triggerWorkflow(NotifierWorkflowTypeEnum::sms->name, $this->formatSubscribers($subscribersId), $data);
    }

    /**
     * Process chat notification
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processChat(string $notifName, string|array $subscribersId, array $data)
    {
        return $this->triggerWorkflow(NotifierWorkflowTypeEnum::chat->name, $this->formatSubscribers($subscribersId), $data);
    }

    /**
     * Process push notification
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processPush(string $notifName, string|array $subscribersId, array $data)
    {
        return $this->triggerWorkflow(NotifierWorkflowTypeEnum::push->name, $this->formatSubscribers($subscribersId), $data);
    }

    /**
     * @param string $workflowName
     * @param array $subscribers
     * @param array $data
     * @param array|null $payload
     * @return mixed|array
     */
    public function triggerWorkflow(string $workflowName, array $subscribers, array $data, array $payload = [])
    {
        $payload['custom_sender'] = config('mail.from.address', config('app.name'));
        $payload['custom_subject'] = completeInterpolateText($this->notificationConfig->title, $data);

        if (in_array($workflowName, [NotifierWorkflowTypeEnum::email->name, NotifierWorkflowTypeEnum::chat->name])) {
            $payload['custom_content'] = completeInterpolateText($this->notificationConfig->message_mail, $data);
        } elseif (in_array($workflowName, [NotifierWorkflowTypeEnum::in_app->name, NotifierWorkflowTypeEnum::push->name])) {
            $payload['custom_content'] = completeInterpolateText($this->notificationConfig->message_in_app, $data);
        } else {
            $payload['custom_content'] = completeInterpolateText($this->notificationConfig->message_sms, $data);
        }

        return Novu::triggerEvent([
            'name' => $workflowName,
            'to' => $subscribers,
            'payload' => $payload,
            // 'tenant' => $this->tenantIdentifier,
        ])->toArray();
    }

    /**
     * @param $data
     * @return mixed|array
     */
    public function updateOrCreateSubscriber(array $data)
    {
        return Novu::createSubscriber([
            'subscriberId' => $data['id'],
            'email' => $data['email'] ?? null,
            'firstName' => $data['firstname'] ?? null,
            'lastName' => $data['lastname'] ?? null,
            'phone' => $data['telephone'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'locale' => config('app.locale'),
            'data' => [
                'tenant_id' => $this->tenantIdentifier,
            ]
        ])->toArray();
    }

    /**
     * @param string|array $subscribersId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function formatSubscribers(string|array $subscribersId)
    {
        if (empty($subscribersId)) {
            throw new \Exception('Subcribers cannot be empty.', 422);
        }

        $subscribers = [];
        if (is_string($subscribersId)) {
            $subscribers = ['subscriberId' => $subscribersId];
        } else {
            foreach ($subscribersId as $subscriberId) {
                $subscribers[] = ['subscriberId' => $subscriberId];
            }
        }

        return $subscribers;
    }
}
