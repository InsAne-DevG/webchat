<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketController extends Controller implements MessageComponentInterface
{
    protected $subscribers = [];
    protected $allowedOrigins;

    public function __construct() {
        $this->allowedOrigins = [env('APP_URL')];
    }

    public function onOpen(ConnectionInterface $conn, Request $request = null) {
        // Check the origin of the connection
        $origin = $conn->httpRequest->getHeader('Origin')[0] ?? null;

        // echo $origin;
        if (!in_array($origin, $this->allowedOrigins)) {
            $conn->close();
            return;
        }
        // New connection opened
        $params = $conn->httpRequest->getUri()->getQuery();
        $queryParams = [];
        parse_str($params, $queryParams);
        $user_id = isset($queryParams['user_id']) ? $queryParams['user_id'] : null;

        if (!$this->authenticateUser($user_id)) {
            $conn->close();
            return;
        }

        User::where('id', $user_id)->update([
            'user_status' => 'online'
        ]);
        $user = User::find($user_id);
        $chat_rooms = $user->chat_rooms();

        foreach ($chat_rooms as $chat_room) {
            $friend = $chat_room->sender !== null ? $chat_room->sender : $chat_room->receiver;
            if($friend->user_status == 'online') {
                $this->publish($this->getUserChannel($friend->id), [
                    'data' => [
                        'type' => 'online_notification',
                        'user_id' => $user_id,
                        'status' => 'online',
                    ]
                ]);
            }
        }
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Received a message
        $data = json_decode($msg, true);

        if ($data['action'] == 'subscribe') {
            if($this->authenticateUser($data['user_id'])){
                $channel = $this->getUserChannel($data['user_id']);
                $this->subscribers[$channel][] = $from;
            } else {
                //disconnect websocket connection
                $from->close();
                return;
            }
        } elseif ($data['action'] == 'publish') {
            $channel = $data['channel'];
            switch ($data['data']['type']) {
                case 'message':
                    Message::create([
                        'chat_room' => $data['data']['chat_room'],
                        'sender_id' => $data['data']['from_user'],
                        'receiver_id' => $data['data']['to_user'],
                        'message' => $data['data']['message']
                    ]);
                    break;
                case 'message_read':
                    Message::where('chat_room', $data['data']['chat_room_id'])
                            ->where('receiver_id', $data['data']['from_user'])
                            ->latest('created_at')
                            ->limit(1)
                            ->update([
                                'is_read' => true
                            ]);
                    break;
            }
            $this->publish($channel, $data);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Connection closed
        echo "Connection {$conn->resourceId} has disconnected\n";
        $channel = $this->getUserChannel($conn->resourceId);
        unset($this->subscribers[$channel]);

        $params = $conn->httpRequest->getUri()->getQuery();
        $queryParams = [];
        parse_str($params, $queryParams);
        $user_id = isset($queryParams['user_id']) ? $queryParams['user_id'] : null;

        User::where('id', $user_id)->update([
            'user_status' => 'offline'
        ]);

        $user = User::find($user_id);
        $chat_rooms = $user->chat_rooms();

        foreach ($chat_rooms as $chat_room) {
            $friend = $chat_room->sender !== null ? $chat_room->sender : $chat_room->receiver;
            if($friend->user_status == 'online') {
                $this->publish($this->getUserChannel($friend->id), [
                    'data' => [
                        'type' => 'online_notification',
                        'user_id' => $user_id,
                        'status' => 'offline',
                    ]
                ]);
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        // Error occurred
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function publish($channel, $data) {
        if (isset($this->subscribers[$channel])) {
            foreach ($this->subscribers[$channel] as $subscriber) {
                $subscriber->send(json_encode([
                    'data' => $data
                ]));
            }
        }
    }

    protected function getUserChannel($userId) {
        // Generate a unique channel for each user
        return "user_{$userId}";
    }

    protected function authenticateUser($userId) {
        return User::where('id', $userId)->exists();
    }
}
