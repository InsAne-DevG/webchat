<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatRoomResource;
use App\Http\Resources\MessageResource;
use App\Models\FriendRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function chat()
    {
        return view('chat.index');
    }

    public function getChatRooms()
    {
        $chatRooms = FriendRequest::where(function ($query) {
                                        $query->where('receiver_id', Auth::id())
                                            ->where('status', 'accepted');
                                    })->orWhere(function ($query) {
                                        $query->where('sender_id', Auth::id())
                                            ->where('status', 'accepted');
                                    })->with([
                                        'sender' => function ($query) {
                                            $query->where('id', '!=', Auth::id());
                                        },
                                        'receiver' => function ($query) {
                                            $query->where('id', '!=', Auth::id());
                                        },
                                        'lastMessage',
                                    ])->get();
        return ChatRoomResource::collection($chatRooms);
    }

    public function getUnreadMessageCount(Request $request)
    {
        $validator = Validator::make($request->only('chat_room'),[
            'chat_room' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        $lastSentMessage = Message::where('sender_id', auth()->id())
                                    ->where('chat_room', $request->chat_room)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        if ($lastSentMessage) {
            $countAfterLastSentMessage = Message::where('chat_room', $request->chat_room)
                                                ->where('created_at', '>', $lastSentMessage->created_at->format('Y-m-d H:i:s'))
                                                ->count();

            return response()->json([
                'count' => $countAfterLastSentMessage
            ]);
        } else {
            $unreadMessageCount = Message::where('chat_room', $request->chat_room)
                                            ->count();
            return response()->json([
                'count' => $unreadMessageCount
            ]);
        }
    }

    public function getMessages(Request $request)
    {
        $validator = Validator::make($request->only('chat_room'),[
            'chat_room' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        $messages = Message::where('chat_room', $request->chat_room)->orderBy('created_at', 'DESC')->get();

        return MessageResource::collection($messages);

    }
}
