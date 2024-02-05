<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\UploadService;
use Illuminate\Http\Request;

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
}
