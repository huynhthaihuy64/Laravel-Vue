<?php

namespace App\Imports;

use App\Models\Goal;
use App\Models\Staff;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportMultipleImport implements ToCollection
{
    public $error = [];

    protected $header = [
        "Mã đối tượng",
        "Mã chỉ tiêu",
        "Thời gian",
        "Tỷ trọng (%)",
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
                throw new Exception(__('messages.validation.import.header-title'));
            } elseif (!empty(array_diff($header, $this->header))) {
                throw new Exception(__('messages.validation.import.header'));
            }
        }
    }
    public function collection(Collection $collection)
    {
        $data = $collection->toArray();
        $staffCodes = collect([]);
        $unitCodes = collect([]);
        $goalCodes = collect([]);

        for ($key = 3; $key < count($data); $key++) {
            $staffCodes->push($data[$key][0]);
            $unitCodes->push($data[$key][0]);
            $goalCodes->push($data[$key][1]);
        }

        $staffs = Staff::whereIn('code', $staffCodes)->get()->keyBy('code');
        $units = Unit::whereIn('code', $unitCodes)->get()->keyBy('code');
        $goals = Goal::whereIn('code', $goalCodes)->get()->keyBy('code');

        try {
            foreach ($data as $key => $array) {
                if ($key == 0 || $key == 1) {
                    continue;
                }
                if ($key == 2) {
                    if ($this->validateRowData($array, $key) instanceof Exception) {
                        break;
                    }
                    continue;
                }

                $this->error[$key] = [
                    'code' => $array[0],
                    'goal_code' => $array[1],
                    'time' => $array[2],
                    'proportion' => $array[3],
                ];

                if (empty($array[0]) || (!$staffs->has($array[0]) && !$units->has($array[0]))) {
                    $this->error[$key]['message'] = "Không tồn tại mã đối tượng";
                    continue;
                }

                if (empty($array[1]) || !$goals->has($array[1])) {
                    $this->error[$key]['message'] = "Không tồn tại mã chỉ tiêu";
                    continue;
                }

                if ((preg_match('/^\d{1,2}\/\d{4}$/', $array[2]) == 0 || preg_match('/^\d{4}$/', $array[2]) == 0) && !empty($array[3]) && preg_match('/^\d+(,\d{1,2})?$/', $array[3]) == 0) {
                    $this->error[$key]['message'] = "Sai định dạng thời gian";
                    continue;
                }

                if (preg_match('/^\d+(,\d{1,2})?$/', $array[3]) == 0 && !empty($array[3])) {
                    $this->error[$key]['message'] = "Sai format tỷ trọng";
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
