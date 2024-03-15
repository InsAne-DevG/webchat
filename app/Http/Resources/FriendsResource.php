<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class FriendsResource extends JsonResource
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
            'id' => $user->id,
            'user_status' => $user->user_status
        ];
    }
}
