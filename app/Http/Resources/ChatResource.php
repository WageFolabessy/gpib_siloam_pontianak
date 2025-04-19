<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Log;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $senderName = 'Unknown';
        $senderLoaded = $this->relationLoaded('sender');
        $senderModel = $this->whenLoaded('sender');

        if ($senderLoaded && $senderModel) {
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
            Log::warning("[ChatResource Clean] Sender relation NOT loaded or failed for Chat ID {$this->id}. sender_id: {$this->sender_id}, sender_type: {$this->sender_type}.");
            if ($this->sender_type === 'user') {
                $senderName = 'User (Relasi Gagal)';
            } elseif ($this->sender_type === 'admin') {
                $senderName = 'Admin (Relasi Gagal)';
            } else {
                $senderName = "Pengirim Error";
            }
        }

        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'sender_type' => $this->sender_type === 'admin' ? 'admin' : 'user',
            'sender_id'   => $this->sender_id,
            'sender_name' => $senderName,
            'message'     => $this->message,
            'read_at'     => $this->read_at?->toIso8601String(),
            'created_at'  => $this->created_at->toIso8601String(),
        ];
    }
}
