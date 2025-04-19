<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesMarkedAsRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public string $readerType;
    public ?int $lastReadMessageId;
    public string $readAtTimestamp;

    public function __construct(int $userId, string $readerType, string $readAtTimestamp, ?int $lastReadMessageId = null)
    {
        $this->userId = $userId;
        $this->readerType = $readerType;
        $this->readAtTimestamp = $readAtTimestamp;
        $this->lastReadMessageId = $lastReadMessageId;
    }


    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('private-chat.user.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'messages.read';
    }
}