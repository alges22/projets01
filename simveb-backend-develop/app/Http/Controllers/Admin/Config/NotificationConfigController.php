<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationConfigRequest;
use App\Models\Account\User;
use App\Models\Config\NotificationConfig;
use App\Notifications\SimvebNotification;
use App\Traits\CrudRepositoryTrait;

class NotificationConfigController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(NotificationConfig::class);
        $this->authorizeResource(NotificationConfig::class); //check if user has permission
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * Display the specified resource.
     */
    public function show(NotificationConfig $notificationConfig)
    {
        return response($notificationConfig->load($notificationConfig::relations()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NotificationConfigRequest $request, NotificationConfig $notificationConfig)
    {
        return response($this->repository->update($notificationConfig, [
            'title' => $request->validated('title'),
            'message_sms' => $request->validated('message_sms'),
            'message_in_app' => $request->validated('message_in_app'),
            'message_mail' => $request->validated('message_mail'),
            'total_repetition_count' => $request->validated('total_repetition_count'),
            'frequency_in_days' => $request->validated('frequency_in_days'),
        ]));
    }

    public function testNotification(NotificationConfigRequest $request)
    {
        if ($request->has('type') && $request->type == 'mail' && $request->has('email')) {
            $user = User::where('email', $request->email)->first();
            $user->notify(new SimvebNotification('plate-color-change-allowed', true, true));

            return response('Notification test successful');
        }

        return response('Notification test error', 500);
    }
}
