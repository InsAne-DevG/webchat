<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use App\Http\Resources\FriendsResource;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function fileUpload(Request $request)
    {
        if($request->isMethod('get'))
        {
            return view('test.file-upload');
        }
        if($request->isMethod('post'))
        {
            FileHelper::storeFiles($request->file('files'), 'test/');
            dd('uplaoded');
        }
    }

    public function chat()
    {
        return view('chat.chat');
    }

    public function userStatus(){
        $user_id = 5;

        $user = User::where('id',$user_id)->update([
            'user_status' => 'online'
        ]);

        dd('done');
    }

    public function friends(){
        $user_id = 5;
        $user = User::find($user_id);
        $chat_rooms = $user->chat_rooms();

        // Send a message to each friend notifying them of the user's online status
        foreach ($chat_rooms as $chat_room) {
            $friend = $chat_room->sender !== null ? $chat_room->sender : $chat_room->receiver;
            if($friend->user_status == 'online') {
                echo "Online";
            }
        }
        // return $friends;
    }
}
