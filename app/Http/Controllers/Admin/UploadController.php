<?php

namespace App\Http\Controllers\Admin;

use App\Constants\FileConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadExcelRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\FileS3Resource;
use Illuminate\Http\Request;
use App\Http\Services\UploadService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UploadController extends Controller
{
    use ResponseTrait;
    protected $uploadService;
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function store(Request $request)
    {
        $url = $this->uploadService->store($request);
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
        $file = $this->uploadService->uploadFileExcel($file, $data);

        return $this->responseSuccess(FileResource::make($file));
    }

    /**
     * Download file by id
     *
     * @param int $id
     * @return BinaryFileResponse
     */
    public function download(int $id): BinaryFileResponse
    {
        $file = $this->uploadService->getFileById($id);

        return response()->download(
            $file['file_path'],
            str_replace(FileConstants::CHARACTERS, '_', $file['file_name']),
            [
                "Access-Control-Expose-Headers" => "Content-Disposition"
            ]
        );
    }

    public function uploadFileS3(UploadExcelRequest $request): JsonResponse
    {
        $data = $request->all();
        $file = $data[FileConstants::INPUT_FILE];
        $data['user_id'] = Auth::id();
        $file = $this->uploadService->uploadFileS3($file, $data);

        //Get All
        // return $this->responseSuccess(FileS3Resource::collection($file));

        //Get 1
        return $this->responseSuccess(FileS3Resource::make($file));
    }
}
