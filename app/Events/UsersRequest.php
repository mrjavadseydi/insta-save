<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsersRequest
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $chat_id,$action,$link;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($chat_id,$action,$link='')
    {
        $this->chat_id = $chat_id;
        $this->action = $action;
        $this->link = $link;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
