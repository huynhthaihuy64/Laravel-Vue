<?php

namespace App\Http\Controllers\Admin;

use App\Constants\FileConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadExcelRequest;
use App\Http\Resources\FileResource;
use Illuminate\Http\Request;
use App\Http\Services\UploadService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
    use ResponseTrait;
    protected $upload;
    public function __construct(UploadService $upload)
    {
        $this->upload = $upload;
    }

    public function store(Request $request)
    {
        // $this->upload->store($request);
        $url = $this->upload->store($request);
        if ($url !== false) {
            return response()->json([
                'error' => false,
                'url' => $url
            ]);
        }

        return response()->json(['error' => true]);
    }

    public function uploadFileExcel(UploadExcelRequest $request): JsonResponse
    {
        $data = $request->all();
        $file = $data[FileConstants::INPUT_FILE];
        $data['user_id'] = Auth::id();
        $data[FileConstants::INPUT_DESTINATION_FOLDER] = FileConstants::FOLDER_TEMP;
        $file = $this->upload->uploadFileExcel($file, $data);

        return $this->responseSuccess(FileResource::make($file));
    }
}
