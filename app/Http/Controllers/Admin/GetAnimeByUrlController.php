<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GetAnimeByUrlController extends Controller
{
    public UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    public function getAnimeByUrl(Request $request)
    {
        $endPoint = "https://api.trace.moe/search?url=" . $request->url;
        $response = HTTP::get($endPoint);
        $anilistId = array_column(($response->json()['result']), 'anilist');
        return response()->json(['data' => $this->uploadService->getAnimeBy($anilistId)]);
    }

    public function getAnimeByImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $tempImagePath = $image->getPathname();

            $response = Http::attach(
                'image',
                file_get_contents($tempImagePath),
                $image->getClientOriginalName()
            )->post('https://api.trace.moe/search?cutBorders&');
            $anilistId = array_column(($response->json()['result']), 'anilist');
            $data = $this->uploadService->getAnimeBy($anilistId);
            unlink($tempImagePath);
            return response()->json(['data' => $data]);
        } else {
            return response()->json(['error' => 'No image provided'], 400);
        }
    }
}
