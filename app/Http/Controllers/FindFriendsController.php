<?php

namespace App\Http\Controllers;

use App\Http\Resources\FriendRequestResource;
use App\Http\Resources\UserCollection;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class FindFriendsController extends Controller
{
    public function index()
    {
        return view('find-friends.index');
    }

    public function search(Request $request)
    {
        $users = User::query();

        $users->where('id', '!=', Auth::id());
        if($request->search_name && $request->search_name != ''){
            $users->where('name', 'like', "%".$request->search_name."%");
        }

        $users = $users->with(['sender', 'receiver'])->paginate(4);


        return UserCollection::collection($users);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->only('id'),[
            'id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        $receiver_id = Crypt::decrypt($request->id);
        $sender_id = Auth::id();
        $existingRequest = FriendRequest::where(function ($query) use ($receiver_id, $sender_id) {
                                                $query->where('receiver_id', $receiver_id)
                                                    ->where('sender_id', $sender_id);
                                            })
                                            ->orWhere(function ($query) use ($receiver_id, $sender_id) {
                                                $query->where('receiver_id', $sender_id)
                                                    ->where('sender_id', $receiver_id);
                                            })
                                            ->exists();
        if($existingRequest){
            return response()->json([
                'is_exists' => true
            ]);
        } else {
            $friendRequest = FriendRequest::create([
                'receiver_id' => $receiver_id,
                'sender_id' => $sender_id
            ]);
            return response()->json([
                'is_exists' => false,
                'request_id' => Crypt::encrypt($friendRequest->id)
            ]);
        }
    }

    public function friendRequests(Request $request)
    {
        if($request->isMethod('get')){
            return view('find-friends.requests');
        }
        if($request->isMethod('post')){
            $validator = Validator::make($request->only('type'),[
                'type' => 'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'errors' => $validator->errors()
                ]);
            }

            if($request->type === 'requests_sent'){
                $friendRequests = FriendRequest::with('receiver')->where('sender_id', Auth::id())->where('status', 'pending')->paginate(1);
            }
            if($request->type === 'requests_received'){
                $friendRequests = FriendRequest::with('sender')->where('receiver_id', Auth::id())->where('status', 'pending')->paginate(1);
            }

            return FriendRequestResource::collection($friendRequests);
        }
    }

    // public function

    public function cancelFriendRequest(Request $request)
    {
        $validator = Validator::make($request->only('id'),[
            'id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        if(FriendRequest::where('id', Crypt::decrypt($request->id))->where('status', 'pending')->delete()){
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function acceptFriendRequest(Request $request)
    {
        $validator = Validator::make($request->only('id'),[
            'id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        if(FriendRequest::where('id', Crypt::decrypt($request->id))->where('status', 'pending')->exists()){
            FriendRequest::where('id', Crypt::decrypt($request->id))->update([
                'status' => 'accepted'
            ]);

            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function rejectFriendRequest(Request $request)
    {
        $validator = Validator::make($request->only('id'),[
            'id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        if(FriendRequest::where('id', Crypt::decrypt($request->id))->where('status', 'pending')->exists()){
            FriendRequest::where('id', Crypt::decrypt($request->id))->update([
                'status' => 'rejected'
            ]);

            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }
}


