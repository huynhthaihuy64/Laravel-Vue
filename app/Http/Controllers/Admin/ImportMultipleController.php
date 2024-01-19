<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Import\ImportService;
use App\Http\Services\UploadService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ImportMultipleController extends Controller
{
    use ResponseTrait;
    protected $uploadService;
    protected $importService;
    public function __construct(UploadService $uploadService, ImportService $importService)
    {
        $this->uploadService = $uploadService;
        $this->importService = $importService;
    }
    public function importMultiple(Request $request)
    {
        if ((int)$request->type === 1) {
            $response = $this->importService->importProduct($request->only(['ids', 'type']));
        } else {
            $response = $this->importService->importMenu($request->only(['id', 'type']));
        }
        return $this->responseSuccess(null, $response);
    }
}
