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
use App\Models\User;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageSent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Chat $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn(): PrivateChannel
    {
        if (!$this->chat->user_id) {
            Log::error('MessageSent event: chat->user_id is null for chat ID: ' . $this->chat->id);
        }
        return new PrivateChannel('private-chat.user.' . $this->chat->user_id);
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $this->chat->loadMissing('sender');

        $senderName = 'Unknown';
        $senderModel = $this->chat->sender;

        if ($senderModel) {
            if ($senderModel instanceof User) {
                $senderName = $senderModel->name;
                if (empty($senderName)) {
                    $senderName = 'User (Nama Kosong)';
                }
            } elseif ($senderModel instanceof AdminUser) {
                $senderName = 'Admin Gereja';
            } else {
                $senderName = 'Tipe Tidak Dikenal';
            }
        } else {
            if ($this->chat->sender_type === 'user') {
                $senderName = 'User (Relasi Gagal)';
            } elseif ($this->chat->sender_type === 'admin') {
                $senderName = 'Admin (Relasi Gagal)';
            } else {
                $senderName = "Pengirim Error";
            }
        }

        return [
            'id'          => $this->chat->id,
            'user_id'     => $this->chat->user_id,
            'sender_type' => $this->chat->sender_type === 'admin' ? 'admin' : 'user',
            'sender_id'   => $this->chat->sender_id,
            'sender_name' => $senderName,
            'message'     => $this->chat->message,
            'read_at'     => $this->chat->read_at?->toIso8601String(),
            'created_at'  => $this->chat->created_at->toIso8601String(),
        ];
    }
}
