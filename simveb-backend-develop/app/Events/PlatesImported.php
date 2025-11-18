<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlatesImported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $staff;
    public $hasFailure;
    public $errorFilePath;

    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @var string
     */
    public $queue = 'default';

    /**
     * Create a new event instance.
     */
    public function __construct($staff, $hasFailure, $errorFilePath = '')
    {
        $this->staff = $staff;
        $this->hasFailure = $hasFailure;
        $this->errorFilePath = $errorFilePath;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('App.Models.User.'. $this->staff->identity->user->id);
    }

    public function broadcastWith()
    {
        return [
            'title' => "Fin de l'enregistrement des plaques",
            'hasFailure' => $this->hasFailure,
            'errorFileUrl' => $this->errorFilePath ? asset($this->errorFilePath) : "",
        ];
    }

    public static function broadcastAs()
    {
        return 'plates-imported';
    }
}
