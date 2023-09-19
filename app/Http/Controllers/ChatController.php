<?php

namespace App\Http\Controllers;

use App\Http\Services\Chat\ChatService;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->chatService->index($request->all());
    }

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function show($userId)
    {
        try {
            return $this->chatService->show($userId);
        } catch(\Exception $err) {
            Log::info($err->getMessage());
            return $err->getMessage();
        }
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function history(Request $request, $friendId)
    {
        try {
            return $this->chatService->history($request->all(), $friendId);
        }
        catch (\Exception $err) {
            Log::info($err->getMessage());
            return $err->getMessage();
        }
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendChat(Request $request)
    {
        return $this->chatService->sendChat($request->all());
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function searchFriend(Request $request)
    {
        return $this->chatService->searchFriend($request->all());
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function addFriend(Request $request)
    {
        return $this->chatService->addFriend($request->all());
    }
}
