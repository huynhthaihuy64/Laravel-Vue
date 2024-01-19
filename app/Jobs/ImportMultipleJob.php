<?php

namespace App\Jobs;

use App\Exports\ExportMultiple;
use App\Imports\ImportMultipleImport;
use App\Models\FileUpload;
use App\Models\Goat;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportMultipleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file;
    protected $fileNameWithExtension;
    protected $userId;

    public function __construct($file, $fileNameWithExtension, $userId)
    {
        $this->file = $file;
        $this->fileNameWithExtension = $fileNameWithExtension;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            $import = new ImportMultipleImport;
            $imported = Excel::import($import, $this->file);

            if (!empty(array_column($import->error, 'message'))) {
                $status = 0;
                Excel::store(new ExportMultiple($import->error), 'uploads/backlog/' . $this->fileNameWithExtension, 'public');
                $url['file_path'] =  '/storage/' . 'uploads/backlog/' . $this->fileNameWithExtension;
            } else {
                $updates = [];
                foreach ($import->error as $val) {
                    $time = $val['time'] ?? '';
                    if (preg_match('/^\d{1,2}\/\d{4}$/', $time)) {
                        $type = 'Month';
                    } elseif (preg_match('/^\d{4}$/', $time)) {
                        $type = 'Year';
                    }
                    
                    $updates[] = [
                        'user_id' => $val['code_object'],
                        'product_id' => $val['code_target'],
                        'time' => $val['time'],
                        'cal_value' => $val['calculated_value'],
                        'real_value' => $val['real_value'],
                        'type' => $type,
                        'updated_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                    ];
                }
                
                Goat::upsert($updates, ['user_id', 'product_id', 'time'], ['cal_value', 'real_value', 'time', 'updated_at', 'type']);
                $status = 1;
                Storage::move($this->file, 'public/uploads/success/' . $this->fileNameWithExtension);
                $url['file_path'] = '/storage/' . 'uploads/success/' . $this->fileNameWithExtension;
            }
            FileUpload::create([
                'name' => $this->fileNameWithExtension,
                'path' => $url['file_path'],
                'user_id' => $this->userId,
                'status' => $status
            ]);
        } catch (\Exception $err) {
            Log::info($err->getMessage());
        }
    }
}
