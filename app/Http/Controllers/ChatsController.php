<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatsController extends Controller
{
    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('chat',[
            'title' => 'Chats',
        ]);
    }

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {

        try {
            $user = auth()->user();

            $message = $user->messages()->create([
                'message' => $request->input('message')
            ]);

            broadcast(new MessageSent($user, $message))->toOthers();

            return ['status' => 'Message Sent!'];
        } catch (\Exception $err) {
            Log::info($err->getMessage());
            return  false;
        }
    }
}
