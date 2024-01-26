<?php

namespace App\Http\Services\Import;

use App\Http\Services\Product\ProductService;
use App\Http\Services\UploadService;
use App\Jobs\ImportProductJob;
use App\Models\File;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportService
{
    protected $product;
    protected $file;
    protected $uploadService;
    protected $productService;

    public function __construct(Product $product, File $file, UploadService $uploadService, ProductService $productService)
    {
        $this->product = $product;
        $this->file = $file;
        $this->uploadService = $uploadService;
        $this->productService = $productService;
    }
    public function importProduct($params)
    {
        try {
            DB::beginTransaction();
            $files = File::whereIn('id', $params['ids'])->get()->toArray();
            foreach ($files as $file) {
                ImportProductJob::dispatch(
                    $file,
                    $this->uploadService,
                    $this->productService,
                    Auth::user()
                );
            }
            DB::commit();
            return __('messages.import.success');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function importMenu($params)
    {
    }
}
