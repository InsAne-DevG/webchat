<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chat_room' => $this->chat_room,
            'is_read' => $this->is_read,
            'media_type' => $this->media_type,
            'media_url' => $this->media_url,
            'message' => $this->message,
            'receiver_id' => $this->receiver_id,
            'sender_id' => $this->sender_id,
            'created_at' => date('d-m-Y g:i:s A', strtotime($this->created_at)),
            'updated_at' => $this->updated_at
        ];
    }
}
