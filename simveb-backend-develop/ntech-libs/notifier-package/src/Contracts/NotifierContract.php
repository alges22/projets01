<?php

namespace Ntech\NotifierPackage\Contracts;

interface NotifierContract
{
    /**
     * Process notification to specified channel
     *
     * @param string $channel
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     *
     */
    public function process(string $channel, string $notifName, string|array $subscribersId, array $data);

    /**
     * Process notification in app
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processInApp(string $notifName, string|array $subscribersId, array $data);

    /**
     * Process email notification
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processEmail(string $notifName, string|array $subscribersId, array $data);

    /**
     * Process sms notification
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processSms(string $notifName, string|array $subscribersId, array $data);

    /**
     * Process chat notification
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processChat(string $notifName, string|array $subscribersId, array $data);

    /**
     * Process push notification
     *
     * @param string $notifName
     * @param string|array $subscribersId
     * @param array $data
     * @return mixed|array
     */
    public function processPush(string $notifName, string|array $subscribersId, array $data);

    /**
     * @param string $workflowName
     * @param array $subscribers
     * @param array $data
     * @param array|null $payload
     * @return mixed|array
     */
    public function triggerWorkflow(string $workflowName, array $subscribers, array $data, array $payload = []);

    /**
     * @param $data
     * @return mixed|array
     */
    public function updateOrCreateSubscriber(array $data);

    /**
     * @param string|array $subscribersId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function formatSubscribers(string|array $subscribersId);
}
