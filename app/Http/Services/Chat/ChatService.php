<?php

namespace App\Http\Services\Chat;

use App\Events\ChatRoomBroadCast;
use App\Exceptions\NotFoundException;
use App\Models\ChatRoom;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        try {
            $users = $this->user->where('id', '!=', Auth::user()->id)->with('unseenMessages')->get();
            return $users;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $e->getMessage();
        }
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
        //Select * from chat_rooms where ((user_id = 1 and receiver_id = 2) or (receiver_id = 1 and user_id = 2)) ORDERBY created_at asc
        $chatRoom = $this->chatRoom->where(function ($query) use ($friendId) {
            $query->where('user_id', Auth::user()->id)
                ->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($friendId) {
            $query->where('friend_id', Auth::user()->id)
                ->where('user_id', $friendId);
        })->orderBy('created_at', 'desc')->limit($params['limit'] ?? 10)
            ->with(['user'])
            ->get();
        
        if ($chatRoom->count()) {
            foreach($chatRoom as $msg) {
                if((int) $msg->friend_id === auth()->user()->id) {
                    $msg->update(['seen' => 2]);
                }
            } 
        }
        return $chatRoom;
    }

    public function sendChat($params)
    {
        DB::beginTransaction();
        try {
            $chatRoom = $this->chatRoom->create([
                'user_id' => Auth::user()->id,
                'friend_id' => $params['friend_id'],
                'chat' => $params['chat']
            ]);
            $chatRoom->load('user');
            $message = $this->chatRoom->with('user','friend')->find($chatRoom->id);
            broadcast(new ChatRoomBroadCast(Auth::user(), $message))->toOthers();
            DB::commit();
            return $params['chat'];
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
        })->where('id', '!=', Auth::user()->id)
            ->get();
        return $users;
    }

    public function addFriend($params)
    {
        DB::beginTransaction();
        try {
            $friend = $this->friend->create([
                'user_id' => Auth::user()->id,
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
