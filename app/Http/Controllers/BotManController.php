<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        $botman = app('botman');
        $botman->hears('{message}', function ($botman, $message) {
            if ($message === 'hi') {
                $botman->startConversation(new OnConversation);
            } else {
                $botman->reply('Start a conversation by saying hi.');
            }
        });
        $botman->listen();
    }
}
