<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class UserCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = 'Add';
        $request_id = null;
        if($this->sender){
            if ($this->sender->contains('receiver_id', Auth::id())) {
                $status = 'Accept';
            }
        }
        if($this->receiver){
            foreach($this->receiver as $recevierObj){
                if($recevierObj->sender_id === Auth::id()) {
                    $status = 'Request Sent';
                    $request_id = Crypt::encrypt($recevierObj->id);
                    break;
                }
            }
        }
        return [
            'id' => Crypt::encrypt($this->id),
            'profile_picture' => $this->profile_picture,
            'name' => $this->name,
            'status' => $status,
            'request_id' => $request_id
        ];
    }
}
