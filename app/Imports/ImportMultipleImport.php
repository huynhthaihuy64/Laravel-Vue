<?php

namespace App\Imports;

use App\Models\FileUpload;
use App\Models\Goat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportMultipleImport implements ToCollection
{
    public $error = [];
    protected $header = [
        "Mã đối tượng",
        "Mã chỉ tiêu",
        "Thời gian",
        "Giá trị tính (%)",
        "Giá trị TT (%)",
    ];

    protected function validateRowData($row, $key)
    {
        $header = [];
        foreach ($row as $h) {
            if (!empty($h)) {
                $header[]  = str_replace(["\r\n", '"', "\n"], '', $h);
            }
        }

        if ($key == 2) {
            if (array_values($row) !== array_values($this->header)) {
                throw new Exception("Header Title Incorrect");
            } elseif (!empty(array_diff($header, $this->header))) {
                throw new Exception("Header Incorrect");
            }
        }
    }

    /**
     * @param  Collection  $collection
     */
    public function collection(Collection $collection)
    {
        try {
            foreach ($collection->toArray() as $key => $array) {
                if ($key == 0 || $key == 1) {
                    continue;
                }
                if ($key == 2) {
                    if ($this->validateRowData($array, $key) instanceof Exception) {
                        break;
                    }
                    continue;
                }
                if ($key >= 3) {
                    $this->error[$key] = [
                        'code_object' => $array[0],
                        'code_target' => $array[1],
                        'time' => $array[2],
                        'calculated_value' => $array[3],
                        'real_value' => $array[4],
                    ];
                }
                if ($key >= 3  && empty($array[0])) {
                    $this->error[$key]['message'] = "Mã đối tượng là bắt buộc";
                    continue;
                }
                if ($key >= 3  && empty($array[1])) {
                    $this->error[$key]['message'] = "Mã chỉ tiêu là bắt buộc";
                    continue;
                }
                if ($key >= 3 && (preg_match('/^\d{1,2}\/\d{4}$/',$array[2]) == 0 && preg_match('/^\d{4}$/', $array[2]) == 0)){
                    $this->error[$key]['message'] = "Format time failed";
                    continue;
                }
            }
            return $this->error;
        } catch (Exception $ex) {
            Log::info('Import Product Fail: ' . $ex->getMessage());
            throw new Exception($ex->getMessage(), 400);
        }
    }
}