<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BlacklistDataImported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $staff;
    public $hasFailure;
    public $errorFilePath;

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
        return new Channel('App.Models.User.' . $this->staff->identity->user->id);
    }

    public function broadcastWith()
    {
        return [
            'title' => "Fin de l'enregistrement sur la liste noire",
            'hasFailure' => $this->hasFailure,
            'errorFileUrl' => $this->errorFilePath ? asset($this->errorFilePath) : "",
        ];
    }

    public static function broadcastAs()
    {
        return 'blacklist-data-imported';
    }
}
