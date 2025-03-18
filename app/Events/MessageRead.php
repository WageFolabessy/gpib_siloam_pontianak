<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $conversation;
    public $sender_type;

    public function __construct($conversation, $sender_type)
    {
        $this->conversation = $conversation;
        $this->sender_type = $sender_type;
    }

    public function broadcastOn()
    {
        return new Channel('chat-room');
    }
}
