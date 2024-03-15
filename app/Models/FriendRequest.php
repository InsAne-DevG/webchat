<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id', 'receiver_id', 'status', 'created_at'
    ];

    public function receiver(){
        return $this->hasOne(User::class, 'id', 'receiver_id');
    }

    public function sender(){
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

    public function lastMessage(){
        return $this->hasOne(Message::class, 'chat_room', 'id')->orderBy('created_at', 'DESC');
    }

}
