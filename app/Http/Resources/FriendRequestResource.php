<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class FriendRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = 'Add';
        if($this->sender_id === Auth::id()){
            $id = $this->receiver->id;
            $name = $this->receiver->name;
            $profile_picture = $this->receiver->profile_picture;
            $request_id = $this->id;
            $status = 'Request Sent';
        }
        if($this->receiver_id === Auth::id()){
            $id = $this->sender->id;
            $name = $this->sender->name;
            $profile_picture = $this->sender->profile_picture;
            $request_id = $this->id;
            $status = 'Accept';
        }

        return [
            'id' => Crypt::encrypt($id),
            'name' => $name,
            'profile_picture' => $profile_picture,
            'request_id' => Crypt::encrypt($request_id),
            'status' => $status
        ];
    }
}
