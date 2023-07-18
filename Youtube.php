<?php

namespace App\Http\Controllers;

use Google\Service\YouTube;
use Google_Client;
use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    public function getVideoThumbnail(Request $request)
    {
        $client = new Google_Client();
        $client->setDeveloperKey(env('GOOGLE_API_KEY'));
        $url = $request->url;
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $videoId = $params['v'];
        $youtube = new YouTube($client);

        $videoListResponse = $youtube->videos->listVideos($request->type, ['id' => $videoId]);

        return response()->json($videoListResponse);
    }
}

'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT'),
    'api_key' => env('GOOGLE_API_KEY'),
],

//env
GOOGLE_CLIENT_ID=867907508492-bgr2og6orrollalmo3vrb5j8mvm0c20a.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT=http://localhost:8083/api/callback/google
GOOGLE_API_KEY=AIzaSyCSsKZAXKoiW5zyy3mdqGlwd2rC2bk65Xs