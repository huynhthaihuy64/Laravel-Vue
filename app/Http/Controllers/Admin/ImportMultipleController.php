<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportMultiple;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Http\Services\UploadService;
use App\Imports\ImportMultipleImport;
use App\Models\FileUpload;
use App\Models\Goat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class ImportMultipleController extends Controller
{
    protected $uploadService;

    protected $fileUpload;
    public function __construct(UploadService $uploadService, FileUpload $fileUpload)
    {
        $this->uploadService = $uploadService;
        $this->fileUpload = $fileUpload;
    }
    public function importMultiple(ImportRequest $request)
    {
        try {
            DB::beginTransaction();
            $zip = new ZipArchive();
            $zipFileName = 'multiple_files.zip';
            $zipFilePath = storage_path($zipFileName);
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                foreach ($request->file('file') as $key => $file) {
                    $url = $this->uploadService->uploadFile($file, 'import');
                    $fileNameWithExtension = time() . '-' . $file->getClientOriginalName();
                    $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
                    $import = new ImportMultipleImport;
                    $imported = Excel::import($import, $file);

                    if (!empty(array_column($import->error, 'message'))) {
                        $status = 0;
                        $zip->addFile(
                            Excel::download(new ExportMultiple($import->error), $fileName . '.xlsx')->getFile(),
                            $fileName . '.xlsx'
                        );
                    } else {
                        foreach($import->error as $val) {
                            //Nên tách ra làm 1 hàm riêng
                            $time = $val['time'] ?? '';
                            if (preg_match('/^\d{1,2}\/\d{4}$/', $time)) {
                                $type = 'Month';
                            } elseif (preg_match('/^\d{4}$/', $time)) {
                                $type = 'Year';
                            }
                            $data = [
                                'user_id' => $val['code_object'],
                                'product_id' => $val['code_target'],
                                'time' => $val['time'],
                                'cal_value' => $val['calculated_value'],
                                'real_value' => $val['real_value'],
                                'type' => $type,
                                'updated_at' => Carbon::now(),
                            ];
                            $item = Goat::where('user_id', $val['code_object'])->where('product_id',$val['code_target'])->where('type',$type)->first();
                            if (empty($item)) {
                                $data['created_at'] = Carbon::now();
                                Goat::create($data);
                            } else {
                                $item->update($data);
                            }
                        }
                        $status = 1;
                    }
                    FileUpload::create([
                        'name' => $fileNameWithExtension,
                        'path' => $url['file_path'],
                        'user_id' => auth()->user()->id,
                        'status' => $status
                    ]);
                }
                $zip->close();
                DB::commit();
                return response()->download($zipFilePath)->deleteFileAfterSend(true);
            }
            return false;
        } catch (\Exception $err) {
            DB::rollback();
            return response()->json(($err->getMessage()), 400);
        }
    }
}
