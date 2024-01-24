<?php

namespace App\Http\Services;

use App\Constants\Constants;
use App\Constants\FileConstants;
use App\Models\File as ModelsFile;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadService
{
    public function uploadFile($file, $path)
    {
        if ($file) {
            $fileName = $file->getClientOriginalName();
            $fileNameUid = time() . "-" . $fileName;
            Storage::disk('public')->put('uploads/' . $path . '/' . $fileNameUid, File::get($file));
            $filePath   = '/storage/' . 'uploads/' . $path . '/' . $fileNameUid;

            return [
                'file_name' => $fileName,
                'file_nameUid' => $fileNameUid,
                'file_path' => $filePath
            ];
        }
    }
    public function getAvatar($request)
    {
        if ($request) {
            try {
                $name = $request->getClientOriginalName();

                $pathFull = 'public/';
                $path = $request->storeAs(
                    $pathFull,
                    $name
                );

                $url = '/storage/' . $name;
                return $url;
            } catch (\Exception $error) {
                return false;
            }
        }
    }

    public function getMultipleFiles($request, $folder)
    {
        $files = $request->file('file');
        if ($request->hasFile('file')) {
            foreach ($files as $file) {
                try {
                    $name = $file->getClientOriginalName();

                    $pathFull = 'public/' . $folder;
                    $path = $file->storeAs(
                        $pathFull,
                        $name
                    );

                    $url[] = '/storage/' . $path;
                } catch (\Exception $error) {
                    return false;
                }
            }
        }
        return $url;
    }

    public function uploadFileExcel(UploadedFile $file, array $data)
    {
        $uploadFolder = FileConstants::UPLOADS_FOLDER  . DIRECTORY_SEPARATOR . $data[FileConstants::INPUT_DESTINATION_FOLDER];

        if (!Storage::exists($uploadFolder)) {
            Storage::makeDirectory($uploadFolder);
        }
        $fileName = $file->getClientOriginalName();
        $extensionFile = $file->getClientOriginalExtension();
        $sizeFile = $file->getSize();
        $name = $this->randomFileName($extensionFile);
        $path = Storage::path($uploadFolder);
        $file->move($path, $name);

        $fileData = [
            FileConstants::USER_ID => $data[FileConstants::USER_ID],
            FileConstants::MODIFIED_BY => $data[FileConstants::MODIFIED_BY] ?? null,
            FileConstants::MODIFIED_FILENAME => $name,
            FileConstants::FILE_NAME => $fileName,
            FileConstants::FILE_PATH => $path . DIRECTORY_SEPARATOR . $name,
            FileConstants::FILE_STATUS => 1,
            FileConstants::FILE_SIZE => $sizeFile,
            FileConstants::TYPE => $data[FileConstants::TYPE],
            FileConstants::FILE_TYPE => $extensionFile,
        ];

        return ModelsFile::create($fileData);
    }

    /**
     * @param string $fileExtension
     *
     * @return string
     */
    private function randomFileName(string $fileExtension): string
    {
        return md5(uniqid(rand(), true)) . '.' . $fileExtension;
    }

    /**
     * Get file path by id
     *
     * @param int $id
     *
     * @return ModelsFile
     */
    public function getFileById(int $id): ModelsFile
    {
        $file = ModelsFile::find($id);
        if (!$file || !file_exists($file->file_path)) {
            throw new Exception('File '. $id .'not found');
        }

        return $file;
    }

    /**
     * Handle Overwrite Excel
     *
     * @param int $typeException
     * @param array $importedData
     * @param string $messageError
     * @param string $filePath
     * @param array $info
     * @return array
     */
    public function handleOverwriteExcel(
        int $typeException,
        array $importedData = null,
        string $messageError = null,
        string $filePath,
        array $info
    ): array {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $column =  Constants::GOAL_PERFORMANCE_FIRST_COLUMN;
        $lastColumn = Constants::GOAL_PERFORMANCE_LAST_COLUMN;
        $columnToDelete = [];
        for ($column; $column !== $lastColumn; $column++) {
            $cellValue = $worksheet->getCell($column . Constants::GOAL_PERFORMANCE_SET_CELL)->getValue();
            if ($cellValue === Constants::CONTENT_ERROR) {
                $columnToDelete[] = $column;
            }
        }
        if (!empty($columnToDelete)) {
            $columnToDelete = array_reverse($columnToDelete);
            foreach ($columnToDelete as $columnIndex) {
                $worksheet->removeColumn($columnIndex);
            }
        }
        if (
            $typeException === Constants::TYPE_SUCCESS
        ) {
            $uploadFolder = Constants::FOLDER_FILE_SUCCESS;
        } else {
            $lastColumnWithData = "";
            $previousColumn = "";
            for ($column = Constants::GOAL_PERFORMANCE_FIRST_COLUMN; $column !== $lastColumn; $column++) {
                $cellValue = $worksheet->getCell($column . Constants::GOAL_PERFORMANCE_SET_CELL)->getValue();
                if ($cellValue !== null && $cellValue !== "") {
                    continue;
                } else {
                    $lastColumnWithData = $column;
                    if (isset($info['column']) && !empty($info['column'])) {
                        $lastColumnWithData = $info['column'];
                    }
                    $previousColumn = chr(ord($lastColumnWithData) - 1);
                    break; // Stop the loop if we hit an empty or null cell
                }
            }
            $previousCell = $previousColumn . $info['cell'];
            $sourceStyle = $worksheet->getStyle($previousCell);
            $destinationCell = $lastColumnWithData . $info['cell'];
            $worksheet->duplicateStyle($sourceStyle, $destinationCell);
            $sourceBorders = $sourceStyle->getBorders();
            $destinationStyle = $worksheet->getStyle($destinationCell);
            $destinationStyle->getBorders()->getTop()->setBorderStyle($sourceBorders->getTop()->getBorderStyle());
            $destinationStyle->getBorders()->getBottom()->setBorderStyle($sourceBorders->getBottom()->getBorderStyle());
            $destinationStyle->getBorders()->getBottom()->setBorderStyle($sourceBorders->getRight()->getBorderStyle());
            $worksheet->setCellValue($destinationCell, Constants::CONTENT_ERROR);
            $goalPerformanceSetColumn = $lastColumnWithData;
            $goalPerformanceOriginalColumn = $previousColumn;
            if ($typeException === Constants::TYPE_ERROR) {
                foreach ($importedData as $key => $item) {
                    $message = $item['message'] ?? '';
                    $keyColumn = $key + 1;
                    $worksheet->setCellValue($goalPerformanceSetColumn . $keyColumn, $message);
                    $style = $worksheet->getStyle($goalPerformanceOriginalColumn  . $keyColumn);
                    $worksheet->duplicateStyle($style, $goalPerformanceSetColumn . $keyColumn);
                }
            }
            if ($typeException === Constants::TYPE_EXCEPTION) {
                foreach ($importedData as $key => $item) {
                    if (count(array_filter($item)) === 0) {
                        continue;
                    }
                    $keyColumn = $key + $info['error_column'];
                    $worksheet->setCellValue($goalPerformanceSetColumn . $keyColumn, $messageError);
                    $style = $worksheet->getStyle($goalPerformanceOriginalColumn . $keyColumn);
                    $worksheet->duplicateStyle($style, $goalPerformanceSetColumn . $keyColumn);
                }
            }
            $uploadFolder = Constants::FOLDER_FILE_FAILED;
        }
        $writer = IOFactory::createWriter($spreadsheet, Constants::XLSX);
        $writer->save($filePath);

        $newFileDetail = $this->changeFolder($uploadFolder, $filePath);
        return $newFileDetail;
    }

    /**
     * Overwrite File Excel
     *
     * @param string $folder
     * @param string $filePath
     * @return array
     */
    private function changeFolder($folder, $filePath): array
    {
        $uploadFolder = FileConstants::UPLOADS_FOLDER  . DIRECTORY_SEPARATOR . $folder;
        if (!Storage::exists($uploadFolder)) {
            Storage::makeDirectory($uploadFolder);
        }
        $path = Storage::path($uploadFolder);

        if (!file_exists($path)) {
            mkdir($path, Constants::PERMISSION, true);
        }

        $newFilePath = $path . DIRECTORY_SEPARATOR . basename($filePath);
        rename($filePath, $newFilePath);
        $newFileDetail = [
            'file_path' => $newFilePath,
            'file_size' => filesize($newFilePath)
        ];
        return $newFileDetail;
    }
    public function uploadFileS3(UploadedFile $file, array $data)
    {
        //Upload File To S3
        $file->storeAs('Images/' . Auth::user()->name, $file->getClientOriginalName(), 's3');
        $fileInfo = Storage::disk('s3')->getMetadata('Images/' . Auth::user()->name . '/' . $file->getClientOriginalName());

        //Get All File
        // $fileInfo = Storage::disk('s3')->listContents('Images/'. Auth::user()->name);
        return $fileInfo;
    }
}
