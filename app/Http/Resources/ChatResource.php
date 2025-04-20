<?php

namespace App\Http\Resources;

use App\Models\AdminUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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
                $senderName = $senderModel->name ?: 'User (Nama Kosong)';
            } elseif ($senderModel instanceof AdminUser) {
                $senderName = 'Admin Gereja';
            } else {
                $senderName = 'Tipe Tidak Dikenal';
            }
        } else {
            if ($this->sender_type === 'user') {
                $senderName = 'User (Relasi Gagal)';
            } elseif ($this->sender_type === 'admin') {
                $senderName = 'Admin (Relasi Gagal)';
            } else {
                $senderName = "Pengirim Error";
            }
        }

        $formatDate = function ($dateValue) {
            if ($dateValue instanceof Carbon) {
                return $dateValue->toIso8601String();
            } elseif (is_string($dateValue)) {
                try {
                    return Carbon::parse($dateValue)->toIso8601String();
                } catch (\Exception $e) {
                    Log::warning("ChatResource: Failed to parse date string: " . $dateValue);
                    return null;
                }
            }
            return null;
        };


        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'sender_type' => $this->sender_type === 'admin' ? 'admin' : 'user',
            'sender_id' => $this->sender_id,
            'sender_name' => $senderName,
            'message' => $this->message,
            'read_at' => $formatDate($this->read_at),
            'created_at' => $formatDate($this->created_at),
        ];
    }
}
