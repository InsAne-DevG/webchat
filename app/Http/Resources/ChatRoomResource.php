<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class ChatRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->sender !== null){
            $user = $this->sender;
        } else {
            $user = $this->receiver;
        }
        return [
            'chat_room_id' => $this->id,
            'user_details' => [
                'id' => $user->id,
                'name' => $user->name,
                'photo' => $user->profile_picture,
            ],
            'last_message' => $this->lastMessage->message ?? NULL
        ];
    }
}
