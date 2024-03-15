<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Resources\FriendsResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'online_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sender(){
        return $this->HasMany(FriendRequest::class, 'sender_id', 'id');
    }

    public function receiver(){
        return $this->HasMany(FriendRequest::class, 'receiver_id', 'id');
    }

    public function chat_rooms(){
        $chat_rooms =  FriendRequest::where(function ($query){
            $query->where('receiver_id', $this->id)
                ->where('status', 'accepted');
        })->orWhere(function ($query){
            $query->where('sender_id', $this->id)
                ->where('status', 'accepted');
        })->with([
            'sender' => function ($query) {
                $query->where('id', '!=', $this->id);
            },
            'receiver' => function ($query) {
                $query->where('id', '!=', $this->id);
            }
        ])->get();
        return $chat_rooms;
    }
}
