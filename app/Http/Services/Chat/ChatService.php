<?php

namespace App\Http\Services\Chat;

use App\Events\ChatRoomBroadCast;
use App\Exceptions\NotFoundException;
use App\Models\ChatRoom;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatService
{
    protected $user;
    protected $friend;
    protected $chatRoom;

    public function __construct(User $user, Friend $friend, ChatRoom $chatRoom)
    {
        $this->user = $user;
        $this->friend = $friend;
        $this->chatRoom = $chatRoom;
    }
    public function index($params)
    {
        $friends = auth()->user()->friends();
        return $friends;
    }

    public function show($userId)
    {
        $friends = $this->user->find($userId);
        if (!$friends) {
            throw new NotFoundException(User::class);
        }
        return $friends;
    }

    public function history($params, $friendId)
    {
        $chatRoom = $this->chatRoom->where(function ($query) use ($friendId) {
            $query->where('user_id', auth()->user()->id)
                ->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($friendId) {
            $query->where('friend_id', auth()->user()->id)
                ->where('user_id', $friendId);
        })->orderBy('created_at', 'asc')
            ->with(['user'])
            ->get();
        return $chatRoom;
    }

    public function sendChat($params)
    {
        DB::beginTransaction();
        try {
            $chatRoom = $this->chatRoom->create([
                'user_id' => $params['user_id'],
                'friend_id' => $params['friend_id'],
                'chat' => $params['chat']
            ]);
            $chatRoom->load('user');
            broadcast(new ChatRoomBroadCast($chatRoom))->toOthers();
            DB::commit();
        } catch (\Exception $err) {
            Log::info($err->getMessage());
            DB::rollback();
            return $err->getMessage();
        }
    }

    public function searchFriend($params)
    {
        $users = $this->user->when(isset($params['keyword']), function ($q) use ($params) {
            $q->whereLike(['name', 'phone', 'email'], $params['keyword']);
        })
            ->get();
        return $users;
    }

    public function addFriend($params)
    {
        DB::beginTransaction();
        try {
            $friend = $this->friend->create([
                'user_id' => $params['user_id'],
                'friend_id' => $params['friend_id'],
            ]);
            DB::commit();
            return $friend;
        } catch (\Exception $err) {
            Log::info($err->getMessage());
            DB::rollback();
            return $err->getMessage();
        }
    }
}
