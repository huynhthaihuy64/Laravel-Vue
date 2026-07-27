<?php

namespace App\Imports;

use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class MenuImport implements ToCollection
{

    protected $header = [
        "ID",
        "Name",
        "Parent_id",
        "Description",
        "Content",
        "Active",
    ];

    protected function validateRowData($row, $key)
    {
        $header = [];
        foreach ($row as $h) {
            if (!empty($h)) {
                $header[]  = str_replace(["\r\n", '"', "\n"], '', $h);
            }
        }

        if ($key == 0) {
            if ($row[0] != $this->header[0] || $row[1] != $this->header[1]) {
                throw new Exception(__('messages.validation.import.header-title'));
            } else if (!empty(array_diff($header,$this->header))) {
                throw new Exception(__('messages.validation.import.header'));
            }
        }
    }

    /**
     * @param  Collection  $collection
     */
    public function collection(Collection $collection)
    {
        try {
            DB::beginTransaction();
            foreach ($collection->toArray() as $key => $array) {
                if ($key == 0) {
                    if ($this->validateRowData($array, $key) instanceof Exception) {
                        break;
                    }
                    continue;
                }
                if ($key >= 1  && empty($array[0])) {
                    throw new Exception('id is required');
                }
                if ($key >= 1  && empty($array[1])) {
                    throw new Exception('name is required');
                }
                if ($key >= 1  && empty($array[5])) {
                    throw new Exception('active is required');
                }
                $menu = [
                    'id' => $array[0],
                    'name' => $array[1],
                    'parent_id' => $array[2],
                    'description' => $array[3],
                    'content' => $array[4],
                    'active' => $array[5],
                    'updated_at' => Carbon::now()
                ];
    
                $item = Menu::where('id', $array[0])->first();
                if (empty($item)) {
                    $menu['created_at'] = Carbon::now();
                    Menu::create($menu);
                } else {
                    $item->update($menu);
                }
            }
            Log::info('Import Menu Success');
            DB::commit();
            return true;
        } catch (Exception $ex) {
            Log::info('Import Menu Fail: ' . $ex->getMessage());
            DB::rollBack();
            throw new Exception($ex->getMessage(), 400);
        }
    }
}
