<?php

namespace Ntech\NotifierPackage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed process(string $channel, string $notifName, string|array $subscribersId, array $data)
 * @method static mixed processInApp(string $notifName, string|array $subscribersId, array $data)
 * @method static mixed processEmail(string $notifName, string|array $subscribersId, array $data)
 * @method static mixed processSms(string $notifName, string|array $subscribersId, array $data)
 * @method static mixed processChat(string $notifName, string|array $subscribersId, array $data)
 * @method static mixed processPush(string $notifName, string|array $subscribersId, array $data)
 * @method static mixed triggerWorkflow(string $workflowName, array $subscribers, array $data, array $payload = [])
 * @method static mixed updateOrCreateSubscriber(array $data)
 * @method static array formatSubscribers(string|array $subscribersId)
 *
 * @see \Ntech\NotifierPackage\Notifier
 */
class Notifier extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'notifier';
    }
}
