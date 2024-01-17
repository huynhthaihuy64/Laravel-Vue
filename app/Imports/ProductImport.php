<?php

namespace App\Imports;

use App\Constants\Constants;
use App\Models\File;
use App\Models\Menu;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
{
    private $dataWithMessage = [];
    private $appearedValues = [];

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected $file,
        protected $uploadService,
        protected $productService,
    ) {
        $this->file = $file;
        $this->uploadService = $uploadService;
        $this->productService = $productService;
    }

    protected $headers = Constants::PRODUCT_IMPORT;

    protected function validateRowData($row, $key)
    {
        $headers = array_map(function ($header) {
            return trim(str_replace(["\r\n", '"', "\n"], '', $header));
        }, $row);

        if ($key === 2) {
            for ($i = 0; $i < count($this->headers); $i++) {
                if ($headers[$i] !== $this->headers[$i]) {
                    throw new Exception(__('Header Không đúng'));
                }
            }
        }
    }
    public function collection(Collection $collection)
    {
        $data = $collection->toArray();
        try {
            foreach ($data as $key => $array) {
                if ($key === 0 || $key === 1) {
                    continue;
                }
                if ($key === 2) {
                    if ($this->validateRowData($array, $key) instanceof Exception) {
                        break;
                    }
                    continue;
                }
                if (count(array_filter($array)) === 0) {
                    continue;
                }
                $array = array_map('trim', $array);
                $checkProduct = Product::where('name',$array[0])->get()->toArray();
                $product = isset($array[0]) && !empty($checkProduct)
                    ? $checkProduct
                    : null;

                $checkMenu = Menu::where('name',$array[1])->get()->toArray();
                $menu = isset($array[1]) && $checkMenu
                    ? $checkMenu
                    : null;
                $this->handleValidate($array, $key, $product, $menu);
            }
            $messages = array_column($this->dataWithMessage, 'message');
            $time = Carbon::now();
            if (empty(array_filter($messages))) {
                $status = 3;
                $uniqueArray = $this->getUniqueErrorData($this->dataWithMessage);
                foreach ($uniqueArray as $item) {
                    $this->handleProductData($item, $time);
                }
                $typeException = Constants::TYPE_SUCCESS;
                $newFileDetail = $this->handleOverwriteExcel($typeException);
            } else {
                $status = 2;
                $uniqueArray = $this->getUniqueErrorData($this->dataWithMessage);
                foreach ($uniqueArray as $item) {
                    if (!empty($item['message'])) {
                        continue;
                    }
                    $this->handleProductData($item, $time);
                }
                $typeException = Constants::TYPE_ERROR;
                $newFileDetail = $this->handleOverwriteExcel($typeException, $this->dataWithMessage);
            }

            $this->uploadFile($newFileDetail, $status);
            Log::info(__('Import Success'));
            return true;
        } catch (Exception $e) {
            // Get System Error Message
            Log::Error('Import Failed: ' . $e->getMessage());
            $typeException = Constants::TYPE_EXCEPTION;
            $status = 2;
            $this->dataWithMessage = array_slice($data, 3);
            $messageError = $e->getMessage();
            $newFileDetail = $this->handleOverwriteExcel($typeException, $this->dataWithMessage, $messageError);
            $this->uploadFile($newFileDetail, $status);
        }
    }

    /**
     * handle validate
     */
    private function handleValidate($array, $key, $product, $menu)
    {
        $this->dataWithMessage[$key] = [
            'name' => $array[0],
            'price' => $array[1],
            'price_sale' => $array[2],
            'active' => $array[3],
            'description' => $array[4],
            'content' => $array[5],
            'file' => $array[6],
            'menu' => $array[7],
            'message' => ''
        ];

        if (empty($array[0])) {
            $this->dataWithMessage[$key]['message'] .= __('Tên sản phẩm không được trống') . ', ';
        }

        if (
            empty($array[1])
        ) {
            $this->dataWithMessage[$key]['message'] .= __('Giá không được bỏ trống') . ', ';
        } elseif (!empty($array[1]) && (!empty($array[2]) && $array[2] > $array[1])) 
        {
            $this->dataWithMessage[$key]['message'] .= __('Giá giảm không được lớn hơn giá bán') . ', ';
        }

        if (!empty($array[1])) {
            if (
                preg_match('/^[+-]?(\d+(\.\d*)?|\.\d+)$/', $array[1]) === 0
            ) {
                $this->dataWithMessage[$key]['message'] .= __('Giá tiền phải là 1 dạng số') . ', ';
            }
        }

        if (!empty($array[2])) {
            if (
                preg_match('/^[+-]?(\d+(\.\d*)?|\.\d+)$/', $array[2]) === 0
            ) {
                $this->dataWithMessage[$key]['message'] .= __('Giá tiền phải là 1 dạng số') . ', ';
            }
        }

        if (!empty($array[3] && (!in_array((int)$array[3],[0,1])))) {
            $this->dataWithMessage[$key]['message'] .= __('Trạng thái chỉ có giá trị 0 hoặc 1') . ', ';
        }
        // Check Duplicate Value
        $valueToCheck = $array[0];
        if (in_array($valueToCheck, $this->appearedValues)) {
            $this->dataWithMessage[$key]['message'] .=  __('Trùng sản phẩm') . ', ';
        } else {
            $this->appearedValues[] = $valueToCheck;
        }
        $this->dataWithMessage[$key]['message'] = rtrim($this->dataWithMessage[$key]['message'], ', ');
        return $this->dataWithMessage[$key];
    }

    /**
     * Extract unique error data from imported data
     */
    private function getUniqueErrorData($importedData)
    {
        return collect($importedData)->unique(function ($item) {
            return $item['name'];
        })->values()->all();
    }

    /**
     * Handle processing of performance data
     */
    private function handleProductData($item, $time)
    {
        $this->productService->handleProductData($item, $time);
    }

    /**
     * Overwrite File Excel
     */
    private function handleOverwriteExcel($typeException, $importedData = [], $messageError = null)
    {
        $info = [
            'cell' => Constants::GOAL_PERFORMANCE_SET_CELL,
            'error_column' => Constants::ERROR_COLUMN,
        ];
        return $this->uploadService
            ->handleOverwriteExcel(
                $typeException,
                $importedData,
                $messageError,
                $this->file['file_path'],
                $info
            );
    }

    /**
     * Upload File History
     */
    private function uploadFile($newFileDetail, $status)
    {
        $data = [
            'file_name' => $this->file['file_name'],
            'file_path' => $newFileDetail['file_path'],
            'file_size' => $newFileDetail['file_size'],
            'modified_file_name' => basename($newFileDetail['file_path']),
            'user_id' => $this->file['user_id'],
            'type' => 1,
            'file_type' => $this->file['file_type'],
            'status' => $status,
            'updated_at' => Carbon::now(),
        ];

        $uploadHistory = File::find($this->file['id']);
        $uploadHistory->update($data);
    }
}
