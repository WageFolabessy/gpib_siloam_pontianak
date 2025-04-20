<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Chat;
use App\Models\User; // Import User

class NewUserMessageForAdmin implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public string $userName;
    public string $messagePreview;
    public string $timestamp;

    public function __construct(Chat $chat)
    {
        $chat->loadMissing('user');

        $this->userId = $chat->user_id;
        $this->userName = $chat->user?->name ?? ('User ID: ' . $chat->user_id);
        $this->messagePreview = \Illuminate\Support\Str::limit($chat->message, 30);
        $this->timestamp = $chat->created_at->toIso8601String();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.user.message';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'message_preview' => $this->messagePreview,
            'timestamp' => $this->timestamp,
        ];
    }
}
