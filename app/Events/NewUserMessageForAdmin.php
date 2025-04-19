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

    /**
     * Create a new event instance.
     *
     * @param Chat $chat The chat message sent by the user.
     */
    public function __construct(Chat $chat)
    {
        // Pastikan relasi user (pemilik thread) sudah di-load jika perlu nama dari sana
        // atau ambil dari sender jika sender adalah User
        $chat->loadMissing('user'); // Load pemilik thread chat

        $this->userId = $chat->user_id;
        $this->userName = $chat->user?->name ?? ('User ID: ' . $chat->user_id); // Ambil nama pemilik thread
        $this->messagePreview = \Illuminate\Support\Str::limit($chat->message, 30); // Pratinjau pesan
        $this->timestamp = $chat->created_at->toIso8601String();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Channel private khusus untuk semua admin
        return [
            new PrivateChannel('admin-notifications'),
        ];
    }

    /**
     * Nama event yang akan di-broadcast.
     * Defaultnya adalah nama class, tapi bisa diubah.
     */
    public function broadcastAs(): string
    {
        return 'new.user.message'; // Nama event custom untuk frontend
    }

    /**
     * Data yang akan di-broadcast.
     */
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
