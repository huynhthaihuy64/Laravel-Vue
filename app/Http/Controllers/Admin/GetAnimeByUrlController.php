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
        return $this->uploadService->getAnimeByUrl($request->only('url'));
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
            )->post('https://api.trace.moe/search?cutBorders');
            unlink($tempImagePath);

            return response()->json(['data' => $response->json()]);
        } else {
            return response()->json(['error' => 'No image provided'], 400);
        }
    }
}
