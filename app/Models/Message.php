<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room', 'sender_id', 'receiver_id', 'message', 'is_read', 'media_type', 'media_url'
    ];

}
