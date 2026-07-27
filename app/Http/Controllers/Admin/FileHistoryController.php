<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Http\Services\File\FileService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class FileHistoryController extends Controller
{
    use ResponseTrait;

    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function getList(Request $request) 
    {
        $data = $request->only([
            'user_id', 'status', 'type', 'name',
            'created_at', 'limit'
        ]);
        $result = $this->fileService->getList($data);
        return $this->responseSuccess(FileResource::collection($result));
    }
}
